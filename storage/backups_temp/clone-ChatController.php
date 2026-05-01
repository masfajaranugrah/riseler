<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Models\Message;
use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ChatController extends Controller
{
    /**
     * Get authenticated user from multiple guards
     */
    private function getAuthUser()
    {
        // Cek guard web dulu (untuk admin)
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }

        // Cek guard customer
        if (Auth::guard('customer')->check()) {
            return Auth::guard('customer')->user();
        }

        return null;
    }

    public function admin()
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return redirect()->route('login');
        }

        // Pastikan yang akses adalah admin
        if (!in_array($user->role, ['administrator', 'admin', 'customer_service'])) {
            return redirect()->route('chat.users')->with('error', 'Akses ditolak');
        }

        // Ambil semua user/pelanggan yang pernah chat dengan siapa saja (termasuk dengan admin)
        $senderIds = Message::distinct()->pluck('sender_id');
        $receiverIds = Message::distinct()->pluck('receiver_id');

        // Gabungkan dan hilangkan duplikat
        $userIdsFromMessages = $senderIds->merge($receiverIds)
            ->unique()
            ->filter(function ($id) use ($user) {
                // Exclude admin sendiri
                return $id !== $user->id;
            })
            ->values();

        // Ambil users yang pernah chat (exclude admin)
        $users = User::whereIn('id', $userIdsFromMessages)
            ->whereNotIn('role', ['administrator', 'admin', 'customer_service'])
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nomer_id' => $user->nomer_id,
                    'type' => 'user',
                    'created_at' => $user->created_at,
                ];
            });

        // Ambil pelanggans yang pernah chat
        $pelanggans = Pelanggan::whereIn('id', $userIdsFromMessages)
            ->get()
            ->map(function ($pelanggan) {
                return [
                    'id' => $pelanggan->id,
                    'name' => $pelanggan->nama_lengkap ?? 'Pelanggan',
                    'nomer_id' => $pelanggan->nomer_id,
                    'type' => 'pelanggan',
                    'created_at' => $pelanggan->created_at,
                ];
            });

        // Gabungkan dan sort by last message time
        $contacts = $users->concat($pelanggans);

        // Sort by last message timestamp (pesan terbaru muncul paling atas)
        $contacts = $contacts->map(function ($contact) {
            // Ambil pesan terakhir dari/ke user ini (tidak harus dengan admin yang login)
            $lastMessage = Message::where(function ($query) use ($contact) {
                $query->where('sender_id', $contact['id'])
                    ->orWhere('receiver_id', $contact['id']);
            })
                ->orderBy('created_at', 'desc')
                ->first();

            $contact['last_message_at'] = $lastMessage ? $lastMessage->created_at : $contact['created_at'];
            return $contact;
        })
            ->sortByDesc('last_message_at')
            ->values();

        return view('content.apps.chat.admin.chat', ['users' => $contacts]);
    }

    public function user()
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return redirect()->route('login');
        }

        // Pastikan yang akses bukan admin
        if (in_array($user->role, ['administrator', 'admin', 'customer_service'])) {
            return redirect()->route('chat.admin')->with('error', 'Silakan gunakan chat admin');
        }

        return view('content.apps.Customer.chat.chat');
    }

    public function getMessages($userId = null)
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Cek apakah user adalah admin
        $isAdmin = isset($user->role) && in_array($user->role, ['administrator', 'admin', 'customer_service']);

        if ($isAdmin && $userId) {
            // Admin melihat chat dengan user/pelanggan tertentu
            $messages = Message::where(function ($query) use ($userId, $user) {
                // Pesan dari user/pelanggan ke admin
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $user->id);
            })
                ->orWhere(function ($query) use ($userId, $user) {
                    // Pesan dari admin ke user/pelanggan
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', $userId);
                })
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            // Pelanggan/User melihat chat dengan admin
            $adminId = User::whereIn('role', ['administrator', 'admin', 'customer_service'])->first()->id ?? null;

            if (!$adminId) {
                return response()->json(['error' => 'Admin not found'], 404);
            }

            $messages = Message::where(function ($query) use ($user, $adminId) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $adminId);
            })
                ->orWhere(function ($query) use ($user, $adminId) {
                    $query->where('sender_id', $adminId)
                        ->where('receiver_id', $user->id);
                })
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return response()->json($messages);
    }

    public function getUserList()
    {
        // Untuk admin mendapatkan list semua kontak (users + pelanggans)
        $users = User::whereNotIn('role', ['administrator', 'admin', 'customer_service'])
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'nomer_id' => $user->nomer_id,
                    'name' => $user->name,
                    'type' => 'user',
                ];
            });

        $pelanggans = Pelanggan::all()->map(function ($pelanggan) {
            return [
                'id' => $pelanggan->id,
                'name' => $pelanggan->nama_lengkap ?? 'Pelanggan',

                'nomer_id' => $pelanggan->nomer_id ?? 'Pelanggan',

                'type' => 'pelanggan',
            ];
        });

        $contacts = $users->concat($pelanggans)->values();

        return response()->json($contacts);
    }

    /**
     * Send a new message
     */
    /**
     * Send a new message (with optional media upload)
     */
    public function send(Request $request)
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Validasi berbeda untuk admin dan user
        $isAdmin = in_array($user->role, ['administrator', 'admin', 'customer_service']);

        // Base validation rules (size driven by env)
        $maxKb = (int) env('CHAT_MEDIA_MAX_KB', 20480); // default 20MB
        $allowedMimes = env('CHAT_MEDIA_MIMES', 'jpg,jpeg,png,gif,webp,mp4,webm,mov');
        $rules = [
            'media' => 'nullable|file|mimes:'.$allowedMimes.'|max:'.$maxKb,
        ];

        if ($isAdmin) {
            $rules['message'] = 'nullable|string|max:5000';
            $rules['receiver_id'] = 'required|string';
        } else {
            $rules['message'] = 'nullable|string|max:5000';
        }

        $request->validate($rules);

        // Either message or media must be present
        if (!$request->message && !$request->hasFile('media')) {
            return response()->json(['error' => 'Message or media is required'], 422);
        }

        if ($isAdmin) {
            $receiverId = $request->receiver_id;

            // Validasi receiver_id ada di users atau pelanggans
            $receiverExists = User::find($receiverId) || Pelanggan::find($receiverId);
            if (!$receiverExists) {
                return response()->json(['error' => 'Receiver not found'], 404);
            }
        } else {
            // User/Pelanggan mengirim ke admin
            $admin = User::whereIn('role', ['administrator', 'admin', 'customer_service'])->first();

            if (!$admin) {
                return response()->json(['error' => 'Admin not found'], 404);
            }

            $receiverId = $admin->id;
        }

        // Handle media upload
        $mediaPath = null;
        $mediaType = null;
        $mediaOriginalName = null;

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $mediaOriginalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());

            // Determine media type
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $mediaType = 'image';
            } elseif (in_array($extension, ['mp4', 'webm', 'mov'])) {
                $mediaType = 'video';
            }

            // Store file
            $mediaPath = $file->store('chat-media', 'public');
        }

        // Simpan message
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message ?? '',
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
            'media_original_name' => $mediaOriginalName,
            'is_read' => false,
        ]);

        // ===== Kirim push notification ke penerima pesan =====
        $receiver = Pelanggan::find($receiverId);
        if (!$receiver) {
            $receiver = User::find($receiverId);
        }

        if ($receiver && $receiver->webpushr_sid) {
            $end_point = 'https://api.webpushr.com/v1/notification/send/sid';

            $http_header = [
                'Content-Type: application/json',
                'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279',
                'webpushrAuthToken: 116294',
            ];

            $senderName = $user->nama_lengkap ?? $user->name ?? 'User';

            $req_data = [
                'title' => 'Pesan Baru',
                'message' => substr($request->message, 0, 50) . (strlen($request->message) > 50 ? '...' : ''),
                'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/chat'),
                'sid' => $receiver->webpushr_sid,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
            curl_setopt($ch, CURLOPT_URL, $end_point);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            Log::info('Webpushr Push Notification', [
                'receiver_id' => $receiverId,
                'webpushr_sid' => $receiver->webpushr_sid,
                'http_code' => $httpCode,
                'response' => $response,
            ]);
        }

        // FIXED: Assign fresh() back to $message
        $message = $message->fresh();
        $message->sender; // Trigger accessor

        Log::info('?? Broadcasting MessageSent', [
            'message_id' => $message->id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'channel' => 'chat.' . $message->receiver_id,
        ]);

        // Broadcast event ke receiver channel
        broadcast(new MessageSent($message));

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 201);
    }



    /**
     * Mark messages as read
     */
    public function markRead($userId)
    {
        try {
            $user = $this->getAuthUser();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $messageIds = [];
            $senderId = null;

            // Cek apakah user adalah admin
            $isAdmin = isset($user->role) && in_array($user->role, ['administrator', 'admin', 'customer_service']);

            if ($isAdmin) {
                // Admin membaca pesan dari user/pelanggan
                // Ambil pesan yang dikirim oleh $userId ke admin ($user->id)
                $messages = Message::where('sender_id', $userId)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->get();

                $messageIds = $messages->pluck('id')->toArray();
                $senderId = $userId; // Yang mengirim pesan adalah user/pelanggan

                if (!empty($messageIds)) {
                    Message::whereIn('id', $messageIds)->update(['is_read' => true]);
                }
            } else {
                // Pelanggan/User membaca pesan dari admin
                // Ambil pesan yang dikirim oleh admin ke pelanggan ($user->id)
                $adminId = User::whereIn('role', ['administrator', 'admin', 'customer_service'])->first()->id ?? null;

                if (!$adminId) {
                    return response()->json(['error' => 'Admin not found'], 404);
                }

                $messages = Message::where('sender_id', $adminId)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->get();

                $messageIds = $messages->pluck('id')->toArray();
                $senderId = $adminId; // Yang mengirim pesan adalah admin

                if (!empty($messageIds)) {
                    Message::whereIn('id', $messageIds)->update(['is_read' => true]);
                }
            }

            // Broadcast read receipt HANYA ke sender (yang mengirim pesan)
            if (!empty($messageIds) && $senderId) {
                try {
                    Log::info('🔔 Broadcasting MessageRead', [
                        'message_ids' => $messageIds,
                        'to_channel' => 'chat.' . $senderId,
                        'sender_id' => $senderId,
                        'reader' => $user->id,
                        'is_admin' => $isAdmin
                    ]);

                    $event = new MessageRead($messageIds, $senderId);
                    broadcast($event);

                    Log::info('✅ MessageRead broadcast sent successfully');
                } catch (\Exception $broadcastError) {
                    Log::error('❌ Broadcast error', [
                        'error' => $broadcastError->getMessage(),
                        'trace' => $broadcastError->getTraceAsString()
                    ]);
                    // Don't fail the request if broadcast fails
                }
            } else {
                Log::warning('⚠️ No broadcast sent', [
                    'messageIds_empty' => empty($messageIds),
                    'senderId_empty' => empty($senderId),
                    'messageIds' => $messageIds,
                    'senderId' => $senderId
                ]);
            }

            return response()->json([
                'success' => true,
                'marked_count' => count($messageIds),
                'message_ids' => $messageIds
            ]);

        } catch (\Exception $e) {
            Log::error('Error in markRead', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread message count
     */
    public function getUnreadCount()
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if (in_array($user->role, ['administrator', 'admin', 'customer_service'])) {
            // Admin: hitung unread per user
            $unreadCounts = Message::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->selectRaw('sender_id, COUNT(*) as count')
                ->groupBy('sender_id')
                ->get()
                ->pluck('count', 'sender_id');

            return response()->json($unreadCounts);
        } else {
            // User: total unread dari admin
            $adminId = User::whereIn('role', ['administrator', 'admin', 'customer_service'])->first()->id ?? null;

            $count = Message::where('sender_id', $adminId)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            return response()->json(['count' => $count]);
        }
    }
}