<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Events\MessageUpdated;
use App\Models\Message;
use App\Models\CustomerFriendship;
use App\Models\CustomerFriendMessage;
use App\Models\User;
use App\Models\Pelanggan;
use App\Jobs\BroadcastAdminChatJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;


class ChatController extends Controller
{
    private const MESSAGE_EDIT_WINDOW_MINUTES = 15;

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

    /**
     * Cached admin ids for chat queries.
     */
    private function getAdminIds(): array
    {
        $ids = User::whereIn('role', ['administrator', 'admin'])
            ->pluck('id')
            ->map(fn ($id) => (string)$id)
            ->filter()
            ->values()
            ->toArray();

        // Safety fallback: include current logged web admin to avoid empty admin scope.
        $currentWebId = Auth::guard('web')->id();
        if ($currentWebId) {
            $ids[] = (string) $currentWebId;
        }

        return array_values(array_unique(array_filter($ids)));
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

        // Ambil hanya kontak dari percakapan CS (chat_type = cs / legacy null).
        $senderIds = Message::where(function ($query) {
            $query->where('chat_type', 'cs')
                ->orWhereNull('chat_type');
        })->distinct()->pluck('sender_id');
        $receiverIds = Message::where(function ($query) {
            $query->where('chat_type', 'cs')
                ->orWhereNull('chat_type');
        })->distinct()->pluck('receiver_id');

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
            // Ambil pesan terakhir dari percakapan CS saja.
            $lastMessage = Message::where(function ($query) use ($contact) {
                $query->where('sender_id', $contact['id'])
                    ->orWhere('receiver_id', $contact['id']);
            })
                ->where(function ($query) {
                    $query->where('chat_type', 'cs')
                        ->orWhereNull('chat_type');
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
            // Admin/CS melihat chat dengan user/pelanggan tertentu
            // Ambil semua admin IDs untuk query pesan
            $adminIds = User::whereIn('role', ['administrator', 'admin', 'customer_service'])
                ->pluck('id')
                ->toArray();

            // Ambil semua pesan antara customer dan admin manapun (CS chat)
            $messages = Message::where(function ($query) use ($userId, $adminIds) {
                $query->where(function ($inner) use ($userId, $adminIds) {
                    // Pesan dari user/pelanggan ke admin manapun
                    $inner->where('sender_id', $userId)
                        ->whereIn('receiver_id', $adminIds);
                })->orWhere(function ($inner) use ($userId, $adminIds) {
                    // Pesan dari admin manapun ke user/pelanggan
                    $inner->whereIn('sender_id', $adminIds)
                        ->where('receiver_id', $userId);
                });
            })
                ->where(function ($query) {
                    // Hanya CS chat (bukan billing chat)
                    $query->where('chat_type', 'cs')
                        ->orWhereNull('chat_type');
                })
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            // Pelanggan/User melihat chat dengan admin
            $adminIds = User::whereIn('role', ['administrator', 'admin', 'customer_service'])
                ->pluck('id')
                ->toArray();

            if (empty($adminIds)) {
                return response()->json(['error' => 'Admin not found'], 404);
            }

            // Ambil semua pesan antara customer dan admin manapun
            $messages = Message::where(function ($query) use ($user, $adminIds) {
                $query->where(function ($inner) use ($user, $adminIds) {
                    $inner->where('sender_id', $user->id)
                        ->whereIn('receiver_id', $adminIds);
                })->orWhere(function ($inner) use ($user, $adminIds) {
                    $inner->whereIn('sender_id', $adminIds)
                        ->where('receiver_id', $user->id);
                });
            })
                ->where(function ($query) {
                    // Hanya CS chat
                    $query->where('chat_type', 'cs')
                        ->orWhereNull('chat_type');
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

        // Base validation rules
        $rules = [
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov|max:20480', // 20MB max
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
            'chat_type' => 'cs', // CS chat type untuk broadcast ke semua customer_service
            'message' => $request->message ?? '',
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
            'media_original_name' => $mediaOriginalName,
            'is_read' => false,
        ]);

        // Jika admin membalas, tandai semua pesan customer sebelumnya sebagai sudah dibaca.
        // Ini mencegah badge unread muncul lagi saat reload berikutnya.
        if ($isAdmin) {
            $adminIds = User::whereIn('role', ['administrator', 'admin', 'customer_service'])
                ->pluck('id')
                ->toArray();

            $readMessageIds = Message::where('sender_id', $receiverId)
                ->whereIn('receiver_id', $adminIds)
                ->where('is_read', false)
                ->where(function ($query) {
                    $query->where('chat_type', 'cs')
                        ->orWhereNull('chat_type');
                })
                ->pluck('id')
                ->toArray();

            if (!empty($readMessageIds)) {
                Message::whereIn('id', $readMessageIds)->update(['is_read' => true]);
                broadcast(new MessageRead($readMessageIds, $receiverId, 'cs'));
            }
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
        try {
            broadcast(new MessageSent($message));
        } catch (\Exception $broadcastEx) {
            Log::error('?? Broadcast MessageSent failed (send)', [
                'error' => $broadcastEx->getMessage(),
                'message_id' => $message->id,
            ]);
        }

        // ===== Kirim push notification ke penerima pesan =====
        // Letakkan SETELAH broadcast agar chat realtime tidak tertahan oleh request eksternal.
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

            $previewText = trim((string) $request->message);
            if ($previewText === '' && $mediaType) {
                $previewText = $mediaType === 'image' ? '[Gambar]' : '[Video]';
            }

            $req_data = [
                'title' => 'Pesan Baru',
                'message' => Str::limit($previewText, 50, '...'),
                'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/chat'),
                'sid' => $receiver->webpushr_sid,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
            curl_setopt($ch, CURLOPT_URL, $end_point);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);

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

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 201);
    }

    /**
     * Edit own CS chat message within limited time window
     */
    public function updateMessage(Request $request, $messageId)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $message = Message::where('id', $messageId)
            ->where(function ($query) {
                $query->where('chat_type', 'cs')
                    ->orWhereNull('chat_type');
            })
            ->first();

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        if ((string)$message->sender_id !== (string)$user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        if ($message->is_deleted) {
            return response()->json(['error' => 'Message already deleted'], 422);
        }

        if ($message->created_at->lt(now()->subMinutes(self::MESSAGE_EDIT_WINDOW_MINUTES))) {
            return response()->json(['error' => 'Edit window expired'], 422);
        }

        $data = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message->update([
            'message' => trim($data['message']),
            'edited_at' => now(),
        ]);

        $message = $message->fresh();
        broadcast(new MessageUpdated($message, 'edited'));

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Delete own CS chat message within limited time window
     */
    public function deleteMessage($messageId)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $message = Message::where('id', $messageId)
            ->where(function ($query) {
                $query->where('chat_type', 'cs')
                    ->orWhereNull('chat_type');
            })
            ->first();

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        if ((string)$message->sender_id !== (string)$user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        if ($message->is_deleted) {
            return response()->json(['error' => 'Message already deleted'], 422);
        }

        if ($message->created_at->lt(now()->subMinutes(self::MESSAGE_EDIT_WINDOW_MINUTES))) {
            return response()->json(['error' => 'Delete window expired'], 422);
        }

        $message->update([
            'message' => 'Pesan dihapus',
            'is_deleted' => true,
            'deleted_at' => now(),
            'media_path' => null,
            'media_type' => null,
            'media_original_name' => null,
            'is_read' => true,
        ]);

        $message = $message->fresh();
        broadcast(new MessageUpdated($message, 'deleted'));

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
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
                // Admin/CS membaca pesan dari user/pelanggan
                // Ambil semua admin IDs karena pesan bisa dikirim ke admin manapun
                $adminIds = User::whereIn('role', ['administrator', 'admin', 'customer_service'])
                    ->pluck('id')
                    ->toArray();

                // Ambil pesan yang dikirim oleh $userId ke admin manapun (CS chat)
                $messages = Message::where('sender_id', $userId)
                    ->whereIn('receiver_id', $adminIds)
                    ->where('is_read', false)
                    ->where(function ($query) {
                        $query->where('chat_type', 'cs')
                            ->orWhereNull('chat_type');
                    })
                    ->get();

                $messageIds = $messages->pluck('id')->toArray();
                $senderId = $userId; // Yang mengirim pesan adalah user/pelanggan

                if (!empty($messageIds)) {
                    Message::whereIn('id', $messageIds)->update(['is_read' => true]);
                }
            } else {
                // Pelanggan/User membaca pesan dari admin
                // Ambil semua pesan dari admin manapun ke pelanggan ini
                $adminIds = User::whereIn('role', ['administrator', 'admin', 'customer_service'])
                    ->pluck('id')
                    ->toArray();

                $messages = Message::whereIn('sender_id', $adminIds)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->where(function ($query) {
                        $query->where('chat_type', 'cs')
                            ->orWhereNull('chat_type');
                    })
                    ->get();

                $messageIds = $messages->pluck('id')->toArray();
                // Untuk read receipt, kirim ke semua admin yang pernah mengirim pesan
                $senderId = $userId; // Pass userId untuk broadcast ke admin channel

                if (!empty($messageIds)) {
                    Message::whereIn('id', $messageIds)->update(['is_read' => true]);
                }
            }

            // Broadcast read receipt HANYA ke sender (yang mengirim pesan)
            if (!empty($messageIds) && $senderId) {
                try {
                    Log::info('?? Broadcasting MessageRead', [
                        'message_ids' => $messageIds,
                        'to_channel' => 'chat.' . $senderId,
                        'sender_id' => $senderId,
                        'reader' => $user->id,
                        'is_admin' => $isAdmin
                    ]);

                    $event = new MessageRead($messageIds, $senderId, 'cs');
                    broadcast($event);

                    Log::info('? MessageRead broadcast sent successfully');
                } catch (\Exception $broadcastError) {
                    Log::error('? Broadcast error', [
                        'error' => $broadcastError->getMessage(),
                        'trace' => $broadcastError->getTraceAsString()
                    ]);
                    // Don't fail the request if broadcast fails
                }
            } else {
                Log::warning('?? No broadcast sent', [
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
            // Admin/CS: hitung unread dari semua pesan CS chat yang belum dibaca
            // Ambil semua admin IDs untuk filter receiver
            $adminIds = User::whereIn('role', ['administrator', 'admin', 'customer_service'])
                ->pluck('id')
                ->toArray();

            // Hitung unread per sender (customer) yang mengirim ke admin manapun
            // dengan chat_type = 'cs' atau null (legacy)
            $unreadCounts = Message::whereIn('receiver_id', $adminIds)
                ->where('is_read', false)
                ->where(function ($query) {
                    $query->where('chat_type', 'cs')
                        ->orWhereNull('chat_type');
                })
                ->selectRaw('sender_id, COUNT(*) as count')
                ->groupBy('sender_id')
                ->get()
                ->pluck('count', 'sender_id');

            return response()->json($unreadCounts);
        } else {
            // User: total unread dari admin
            $adminIds = User::whereIn('role', ['administrator', 'admin', 'customer_service'])
                ->pluck('id')
                ->toArray();

            $count = Message::whereIn('sender_id', $adminIds)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->where(function ($query) {
                    $query->where('chat_type', 'cs')
                        ->orWhereNull('chat_type');
                })
                ->count();

            return response()->json(['count' => $count]);
        }
    }

    // ===================================================================
    // ADMIN BILLING CHAT METHODS (Separate from CS Chat)
    // Only for admin and administrator roles - NOT customer_service
    // ===================================================================

    /**
     * Admin Billing Chat View - List customers who chatted with admin
     */
    public function adminBilling()
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return redirect()->route('login');
        }

        // Only admin and administrator can access (NOT customer_service)
        if (!in_array($user->role, ['administrator', 'admin'])) {
            return redirect()->route('dashboard.welcome')->with('error', 'Akses ditolak');
        }

        // Query dioptimalkan: sorting "pesan terakhir" dikerjakan di DB.
        // Jangan pakai cache agar daftar pelanggan selalu sinkron.
        $adminIds = $this->getAdminIds();
        $adminIdsQuoted = "'" . implode("','", $adminIds ?: ['-']) . "'";

        $lastMessageSubquery = Message::query()
            ->where('chat_type', 'admin')
            ->where(function ($query) use ($adminIds) {
                $query
                    ->where(function ($subQ) use ($adminIds) {
                        // Admin -> pelanggan (receiver wajib ada)
                        $subQ->whereIn('sender_id', $adminIds)
                            ->whereNotNull('receiver_id');
                    })
                    ->orWhere(function ($subQ) use ($adminIds) {
                        // Pelanggan -> admin (dukungan data lama: receiver_id bisa NULL)
                        $subQ->whereNotIn('sender_id', $adminIds)
                            ->where(function ($innerQ) use ($adminIds) {
                                $innerQ->whereIn('receiver_id', $adminIds)
                                    ->orWhereNull('receiver_id');
                            });
                    });
            })
            ->selectRaw("
                CASE
                    WHEN sender_id IN ($adminIdsQuoted) THEN receiver_id
                    ELSE sender_id
                END as contact_id,
                MAX(created_at) as last_at
            ")
            ->groupBy('contact_id');

        $contacts = Pelanggan::query()
            ->leftJoinSub($lastMessageSubquery, 'lm', function ($join) {
                $join->on('pelanggans.id', '=', 'lm.contact_id');
            })
            ->select([
                'pelanggans.id',
                DB::raw("COALESCE(pelanggans.nama_lengkap, 'Pelanggan') as name"),
                'pelanggans.nomer_id',
                'pelanggans.created_at',
                DB::raw('COALESCE(lm.last_at, pelanggans.created_at) as last_message_at'),
            ])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'nomer_id' => $item->nomer_id,
                    'type' => 'pelanggan',
                    'created_at' => $item->created_at,
                    'last_message_at' => $item->last_message_at,
                ];
            })
            ->values()
            ->all();

        return view('content.apps.chat.admin-billing.chat', ['users' => $contacts]);
    }

    public function adminBillingBroadcast()
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->role, ['administrator', 'admin'])) {
            return redirect()->route('dashboard.welcome')->with('error', 'Akses ditolak');
        }

        return view('content.apps.chat.admin-billing.broadcast');
    }

    /**
     * Customer view to chat with Admin for billing
     */
    public function customerBillingChat()
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return redirect()->route('users.member');
        }

        return view('content.apps.Customer.chat.chat-billing');
    }

    /**
     * Helper: send push notification via Webpushr
     */
    private function sendWebpushrNotification($sid, $title, $message, $targetUrl)
    {
        if (!$sid) return;

        $end_point = 'https://api.webpushr.com/v1/notification/send/sid';
        $http_header = [
            'Content-Type: application/json',
            'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279',
            'webpushrAuthToken: 116294',
        ];

        $req_data = [
            'title' => $title,
            'message' => substr($message, 0, 90) . (strlen($message) > 90 ? '...' : ''),
            'target_url' => $targetUrl,
            'sid' => $sid,
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

        Log::info('Webpushr Broadcast', [
            'sid' => $sid,
            'http_code' => $httpCode,
            'response' => $response,
        ]);
    }

    /**
     * Broadcast billing chat message to all pelanggan
     * Supports: greeting (pagi/siang/sore/malam), quote, billing reminder, custom
     */
    public function broadcastAdminChat(Request $request)
    {
        $user = $this->getAuthUser();

        if (!$user || !in_array($user->role ?? '', ['administrator', 'admin'])) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Allow long-running broadcast (user requested 2 days / 172800 seconds)
        @set_time_limit(172800);

        $data = $request->validate([
            'type' => 'required|string|in:greeting,quote,billing,custom',
            'variant' => 'nullable|string|in:pagi,siang,sore,malam',
            'message' => 'nullable|string|max:5000',
        ]);

        $type = $data['type'];
        $variant = $data['variant'] ?? null;
        $incomingMessage = $data['message'] ?? null;

        $broadcastId = Str::uuid()->toString();
        $cacheKey = "broadcast_admin_{$broadcastId}";

        $quotes = [
            'Jangan menunggu waktu yang tepat, buatlah waktu itu tepat dengan tindakanmu.',
            'Setiap hari adalah peluang baru untuk menjadi lebih baik.',
            'Konsistensi kecil hari ini, hasil besar esok hari.',
            'Syukuri yang ada, upayakan yang belum ada.',
            'Langkah kecil hari ini lebih baik daripada rencana besar yang tidak dimulai.',
        ];

        $messageText = null;

        switch ($type) {
            case 'greeting':
                $variant = $variant ?? 'pagi';
                $templates = [
                    'pagi' => 'Selamat pagi! Semoga hari Anda menyenangkan. Jika ada pertanyaan tagihan, kami siap membantu.',
                    'siang' => 'Selamat siang! Semoga aktivitas Anda lancar. Jangan ragu hubungi kami untuk bantuan pembayaran.',
                    'sore' => 'Selamat sore! Semoga hari Anda produktif. Kami tersedia jika ada kendala tagihan.',
                    'malam' => 'Selamat malam! Terima kasih telah menggunakan layanan kami. Hubungi kami jika butuh bantuan pembayaran.',
                ];
                $messageText = $templates[$variant] ?? $templates['pagi'];
                break;

            case 'quote':
                $messageText = $incomingMessage ?: $quotes[array_rand($quotes)];
                break;

            case 'billing':
                $messageText = $incomingMessage ?: 'Pengingat tagihan: Tagihan layanan Anda sudah terbit. Mohon lakukan pembayaran agar layanan tetap aktif. Terima kasih.';
                break;

            case 'custom':
                if (!$incomingMessage) {
                    return response()->json(['error' => 'Message is required for custom broadcast'], 422);
                }
                $messageText = $incomingMessage;
                break;
        }

        $adminId = $user->id;
        $totalSent = 0;
        $totalPelanggan = Pelanggan::count();

        if ($totalPelanggan === 0) {
            return response()->json([
                'success' => false,
                'error' => 'Tidak ada pelanggan untuk broadcast',
            ], 400);
        }

        // Init progress cache
        Cache::put($cacheKey, [
            'status' => 'running',
            'done' => 0,
            'total' => $totalPelanggan,
            'message' => $messageText,
            'started_at' => now()->toIso8601String(),
        ], now()->addHours(6));

        // Dispatch background job so admin tetap bisa melanjutkan aktivitas
        Log::info('BroadcastAdminChat dispatched', [
            'broadcast_id' => $broadcastId,
            'admin_id' => $adminId,
            'total_pelanggan' => $totalPelanggan,
        ]);

        BroadcastAdminChatJob::dispatch(
            $broadcastId,
            $adminId,
            $messageText,
            $totalPelanggan
        );

        return response()->json([
            'success' => true,
            'sent' => 0,
            'message' => $messageText,
            'type' => $type,
            'broadcast_id' => $broadcastId,
            'total' => $totalPelanggan,
        ]);
    }

    public function getBroadcastProgress($id)
    {
        $user = $this->getAuthUser();

        if (!$user || !in_array($user->role ?? '', ['administrator', 'admin'])) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = Cache::get("broadcast_admin_{$id}");

        if (!$data) {
            return response()->json(['error' => 'Not found'], 404);
        }

        Log::info('BroadcastAdminChat progress fetch', [
            'broadcast_id' => $id,
            'status' => $data['status'] ?? null,
            'done' => $data['done'] ?? null,
            'total' => $data['total'] ?? null,
        ]);

        return response()->json($data);
    }

    /**
     * Get Admin Chat Messages (chat_type = 'admin')
     */
    public function getAdminChatMessages(Request $request, $userId = null)
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $isAdmin = isset($user->role) && in_array($user->role, ['administrator', 'admin']);
        $loadAll = $request->boolean('all', false);
        $requestedLimit = (int)$request->query('limit', 150);
        $safeLimit = max(20, min(($requestedLimit > 0 ? $requestedLimit : 150), 500));
        $limit = $loadAll ? null : $safeLimit;

        // Get ALL admin IDs for multi-admin support
        $adminIds = $this->getAdminIds();

        $query = Message::query()
            ->where('chat_type', 'admin')
            ->where(function ($outerQuery) use ($isAdmin, $userId, $user, $adminIds) {
                $targetCustomerId = $isAdmin && $userId ? (string)$userId : (string)$user->id;

                $outerQuery->where(function ($q) use ($targetCustomerId, $adminIds) {
                    // A: pesan dari customer ke admin (receiver bisa null legacy)
                    $q->where('sender_id', $targetCustomerId)
                        ->where(function ($innerQ) use ($adminIds) {
                            $innerQ->whereIn('receiver_id', $adminIds)
                                ->orWhereNull('receiver_id');
                        });
                })->orWhere(function ($q) use ($targetCustomerId, $adminIds) {
                    // B: pesan dari admin ke customer
                    $q->whereIn('sender_id', $adminIds)
                        ->where('receiver_id', $targetCustomerId);
                })->orWhere(function ($q) use ($targetCustomerId, $adminIds) {
                    // C: pesan legacy tanpa receiver, tapi sender admin (misal broadcast gagal set receiver)
                    $q->whereIn('sender_id', $adminIds)
                        ->whereNull('receiver_id')
                        ->where('sender_id', '!=', $targetCustomerId);
                });
            })
            ->orderByDesc('created_at');

        if ($limit !== null) {
            $query->limit($limit);
        }

        // Select kolom minimal agar payload lebih ringan
        $messages = $query->get([
            'id',
            'sender_id',
            'receiver_id',
            'chat_type',
            'message',
            'media_path',
            'media_type',
            'media_original_name',
            'is_read',
            'is_deleted',
            'edited_at',
            'deleted_at',
            'created_at',
            'updated_at',
        ])->reverse()->values();

        // Preload sender info sekali (hindari accessor N+1 dari $appends)
        $senderIds = $messages->pluck('sender_id')->filter()->map(fn ($id) => (string)$id)->unique()->values();

        $usersById = User::query()
            ->whereIn('id', $senderIds)
            ->select('id', 'name', 'email', 'role')
            ->get()
            ->keyBy(fn ($item) => (string)$item->id);

        $pelanggansById = Pelanggan::query()
            ->whereIn('id', $senderIds)
            ->select('id', 'nama_lengkap', 'nomer_id')
            ->get()
            ->keyBy(fn ($item) => (string)$item->id);

        $payload = $messages->map(function ($message) use ($usersById, $pelanggansById) {
            $senderId = (string)$message->sender_id;
            $sender = null;

            if ($usersById->has($senderId)) {
                $u = $usersById->get($senderId);
                $sender = [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email ?? null,
                    'role' => $u->role ?? null,
                ];
            } elseif ($pelanggansById->has($senderId)) {
                $p = $pelanggansById->get($senderId);
                $sender = [
                    'id' => $p->id,
                    'name' => $p->nama_lengkap ?? 'Pelanggan',
                    'email' => $p->nomer_id ?? null, // gunakan nomer_id sebagai identifikasi
                    'role' => 'pelanggan',
                ];
            }

            return [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'chat_type' => $message->chat_type,
                'message' => $message->message ?? '',
                'media_type' => $message->media_type,
                'media_original_name' => $message->media_original_name,
                'media_url' => $message->media_path ? '/storage/' . ltrim($message->media_path, '/') : null,
                'is_read' => (bool)$message->is_read,
                'is_deleted' => (bool)$message->is_deleted,
                'edited_at' => optional($message->edited_at)->toISOString(),
                'deleted_at' => optional($message->deleted_at)->toISOString(),
                'created_at' => optional($message->created_at)->toISOString(),
                'updated_at' => optional($message->updated_at)->toISOString(),
                'sender' => $sender,
            ];
        });

        return response()->json($payload->values());
    }

    /**
     * Send Admin Chat Message (chat_type = 'admin')
     */
    public function sendAdminChat(Request $request)
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $isAdmin = in_array($user->role ?? '', ['administrator', 'admin']);

        $rules = [
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov|max:20480',
        ];

        if ($isAdmin) {
            $rules['message'] = 'nullable|string|max:5000';
            $rules['receiver_id'] = 'required|string';
        } else {
            $rules['message'] = 'nullable|string|max:5000';
        }

        $request->validate($rules);

        if (!$request->message && !$request->hasFile('media')) {
            return response()->json(['error' => 'Message or media is required'], 422);
        }

        if ($isAdmin) {
            $receiverId = $request->receiver_id;
            $receiverExists = Pelanggan::find($receiverId);
            if (!$receiverExists) {
                return response()->json(['error' => 'Receiver not found'], 404);
            }
        } else {
            // Customer sends to admin (not CS)
            $admin = User::whereIn('role', ['administrator', 'admin'])->first();
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

            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $mediaType = 'image';
            } elseif (in_array($extension, ['mp4', 'webm', 'mov'])) {
                $mediaType = 'video';
            }

            $mediaPath = $file->store('chat-media', 'public');
        }

        // Create message with chat_type = 'admin'
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'chat_type' => 'admin', // KEY: This separates from CS chat
            'message' => $request->message ?? '',
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
            'media_original_name' => $mediaOriginalName,
            'is_read' => false,
        ]);

        // Push notification
        $receiver = Pelanggan::find($receiverId);
        if ($receiver && $receiver->webpushr_sid) {
            $end_point = 'https://api.webpushr.com/v1/notification/send/sid';
            $http_header = [
                'Content-Type: application/json',
                'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279',
                'webpushrAuthToken: 116294',
            ];

            $req_data = [
                'title' => 'Pesan dari Admin (Billing)',
                'message' => substr($request->message, 0, 50) . (strlen($request->message) > 50 ? '...' : ''),
                'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/chat-billing'),
                'sid' => $receiver->webpushr_sid,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
            curl_setopt($ch, CURLOPT_URL, $end_point);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }

        $message = $message->fresh();
        $message->sender;

        // Broadcast to admin-billing channel
        try {
            broadcast(new MessageSent($message));
        } catch (\Exception $broadcastEx) {
            Log::error('?? Broadcast MessageSent failed (sendAdminChat)', [
                'error' => $broadcastEx->getMessage(),
                'message_id' => $message->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 201);
    }

    /**
     * Edit own admin billing message within limited time window
     */
    public function updateAdminChatMessage(Request $request, $messageId)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $message = Message::where('id', $messageId)
            ->where('chat_type', 'admin')
            ->first();

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        if ((string)$message->sender_id !== (string)$user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        if ($message->is_deleted) {
            return response()->json(['error' => 'Message already deleted'], 422);
        }

        if ($message->created_at->lt(now()->subMinutes(self::MESSAGE_EDIT_WINDOW_MINUTES))) {
            return response()->json(['error' => 'Edit window expired'], 422);
        }

        $data = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message->update([
            'message' => trim($data['message']),
            'edited_at' => now(),
        ]);

        $message = $message->fresh();
        broadcast(new MessageUpdated($message, 'edited'));

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Delete own admin billing message within limited time window
     */
    public function deleteAdminChatMessage($messageId)
    {
        $user = $this->getAuthUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $message = Message::where('id', $messageId)
            ->where('chat_type', 'admin')
            ->first();

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        if ((string)$message->sender_id !== (string)$user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        if ($message->is_deleted) {
            return response()->json(['error' => 'Message already deleted'], 422);
        }

        if ($message->created_at->lt(now()->subMinutes(self::MESSAGE_EDIT_WINDOW_MINUTES))) {
            return response()->json(['error' => 'Delete window expired'], 422);
        }

        $message->update([
            'message' => 'Pesan dihapus',
            'is_deleted' => true,
            'deleted_at' => now(),
            'media_path' => null,
            'media_type' => null,
            'media_original_name' => null,
            'is_read' => true,
        ]);

        $message = $message->fresh();
        broadcast(new MessageUpdated($message, 'deleted'));

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Mark Admin Chat as Read
     */
    public function markReadAdminChat($userId)
    {
        try {
            $user = $this->getAuthUser();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $isAdmin = isset($user->role) && in_array($user->role, ['administrator', 'admin']);

            if ($isAdmin) {
                $adminIds = $this->getAdminIds();

                // Mark ALL messages from this customer to ANY admin as read
                $messages = Message::where('chat_type', 'admin')
                    ->where('sender_id', $userId)
                    ->where(function ($query) use ($adminIds) {
                        $query->whereIn('receiver_id', $adminIds)
                            ->orWhereNull('receiver_id');
                    })
                    ->where('is_read', false)
                    ->where('is_deleted', false)
                    ->get();

                $messageIds = $messages->pluck('id')->toArray();
                $senderId = $userId;

                if (!empty($messageIds)) {
                    Message::whereIn('id', $messageIds)->update(['is_read' => true]);
                }
            } else {
                $adminIds = $this->getAdminIds();

                if (empty($adminIds)) {
                    return response()->json(['error' => 'Admin not found'], 404);
                }

                $messages = Message::where('chat_type', 'admin')
                    ->whereIn('sender_id', $adminIds)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->where('is_deleted', false)
                    ->get();

                $messageIds = $messages->pluck('id')->toArray();
                $senderId = $adminIds[0] ?? null;

                if (!empty($messageIds)) {
                    Message::whereIn('id', $messageIds)->update(['is_read' => true]);
                }
            }

            if (!empty($messageIds) && $senderId) {
                $event = new MessageRead($messageIds, $senderId, 'admin');
                broadcast($event);
            }

            return response()->json([
                'success' => true,
                'marked_count' => count($messageIds ?? []),
            ]);

        } catch (\Exception $e) {
            Log::error('Error in markReadAdminChat', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Admin Chat Unread Count
     */
    public function getAdminChatUnreadCount()
    {
        $user = $this->getAuthUser();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if (in_array($user->role ?? '', ['administrator', 'admin'])) {
            $adminIds = $this->getAdminIds();

            // Get unread messages sent TO any admin (billing messages from customers)
            $unreadCounts = Message::where('chat_type', 'admin')
                ->where(function ($query) use ($adminIds) {
                    $query->whereIn('receiver_id', $adminIds)
                        ->orWhereNull('receiver_id');
                })
                ->where('is_read', false)
                ->where('is_deleted', false)
                ->selectRaw('sender_id, COUNT(*) as count')
                ->groupBy('sender_id')
                ->get()
                ->pluck('count', 'sender_id');

            return response()->json($unreadCounts);
        } else {
            $adminIds = $this->getAdminIds();

            $count = Message::where('chat_type', 'admin')
                ->whereIn('sender_id', $adminIds)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->where('is_deleted', false)
                ->count();

            return response()->json(['count' => $count]);
        }
    }

    /**
     * Get Admin Chat User List (for admin panel)
     */
    public function getAdminChatUserList()
    {
        $user = $this->getAuthUser();

        if (!$user || !in_array($user->role ?? '', ['administrator', 'admin'])) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $adminIds = $this->getAdminIds();
        $adminIdsQuoted = "'" . implode("','", $adminIds ?: ['-']) . "'";

        $lastMessageSubquery = Message::query()
            ->where('chat_type', 'admin')
            ->where(function ($query) use ($adminIds) {
                $query
                    ->where(function ($subQ) use ($adminIds) {
                        // Admin -> pelanggan (receiver wajib ada)
                        $subQ->whereIn('sender_id', $adminIds)
                            ->whereNotNull('receiver_id');
                    })
                    ->orWhere(function ($subQ) use ($adminIds) {
                        // Pelanggan -> admin (dukungan data lama: receiver_id bisa NULL)
                        $subQ->whereNotIn('sender_id', $adminIds)
                            ->where(function ($innerQ) use ($adminIds) {
                                $innerQ->whereIn('receiver_id', $adminIds)
                                    ->orWhereNull('receiver_id');
                            });
                    });
            })
            ->selectRaw("
                CASE
                    WHEN sender_id IN ($adminIdsQuoted) THEN receiver_id
                    ELSE sender_id
                END as contact_id,
                MAX(created_at) as last_at
            ")
            ->groupBy('contact_id');

        $pelanggans = Pelanggan::query()
            ->leftJoinSub($lastMessageSubquery, 'lm', function ($join) {
                $join->on('pelanggans.id', '=', 'lm.contact_id');
            })
            ->select([
                'pelanggans.id',
                DB::raw("COALESCE(pelanggans.nama_lengkap, 'Pelanggan') as name"),
                'pelanggans.nomer_id',
                'pelanggans.created_at',
                DB::raw('COALESCE(lm.last_at, pelanggans.created_at) as last_message_at'),
            ])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'nomer_id' => $item->nomer_id ?? 'N/A',
                    'type' => 'pelanggan',
                    'last_message_at' => $item->last_message_at,
                ];
            })
            ->values();

        return response()->json($pelanggans);
    }

    // ===================================================================
    // CUSTOMER SOCIAL CHAT METHODS (customer <-> customer)
    // ===================================================================

    public function customerFriendChat()
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return redirect()->route('users.member');
        }

        return view('content.apps.Customer.social.friends-chat');
    }

    public function customerFriendChatRoom($friendId)
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return redirect()->route('users.member');
        }

        $friendship = CustomerFriendship::acceptedBetweenUsers($user->id, $friendId);
        if (!$friendship) {
            return redirect()->route('customer.friends.chat')
                ->with('error', 'Anda belum berteman dengan pelanggan ini.');
        }

        $friend = Pelanggan::find($friendId);
        if (!$friend) {
            return redirect()->route('customer.friends.chat')
                ->with('error', 'Pelanggan tidak ditemukan.');
        }

        return view('content.apps.Customer.social.chat-room', [
            'friend' => $friend,
        ]);
    }

    public function getCustomerFriendData()
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $friendships = CustomerFriendship::query()
            ->where(function ($query) use ($user) {
                $query->where('user_one_id', $user->id)
                    ->orWhere('user_two_id', $user->id);
            })
            ->get();

        $friendIds = [];
        foreach ($friendships->where('status', 'accepted') as $friendship) {
            $friendIds[] = $friendship->user_one_id === $user->id ? $friendship->user_two_id : $friendship->user_one_id;
        }
        $friendIds = collect($friendIds)->unique()->values();

        $friends = Pelanggan::whereIn('id', $friendIds)->get()->keyBy('id');

        $friendList = $friendIds->map(function ($friendId) use ($friends, $user) {
            $friend = $friends->get($friendId);
            if (!$friend) {
                return null;
            }

            $lastMessage = CustomerFriendMessage::query()
                ->where(function ($query) use ($user, $friendId) {
                    $query->where('sender_id', $user->id)->where('receiver_id', $friendId);
                })
                ->orWhere(function ($query) use ($user, $friendId) {
                    $query->where('sender_id', $friendId)->where('receiver_id', $user->id);
                })
                ->latest('created_at')
                ->first();

            $unreadCount = CustomerFriendMessage::query()
                ->where('sender_id', $friendId)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            return [
                'id' => $friend->id,
                'name' => $friend->nama_lengkap ?? 'Pelanggan',
                'nomer_id' => $friend->nomer_id,
                'last_message' => $lastMessage?->message,
                'last_message_at' => optional($lastMessage?->created_at)->toIso8601String(),
                'unread_count' => (int) $unreadCount,
            ];
        })->filter()->sortByDesc(function ($friend) {
            return $friend['last_message_at'] ?? '';
        })->values();

        $incomingRequests = CustomerFriendship::query()
            ->where('addressee_id', $user->id)
            ->where('status', 'pending')
            ->latest('created_at')
            ->get()
            ->map(function ($request) {
                $requester = Pelanggan::find($request->requester_id);
                return [
                    'friendship_id' => $request->id,
                    'id' => $requester?->id,
                    'name' => $requester?->nama_lengkap ?? 'Pelanggan',
                    'nomer_id' => $requester?->nomer_id,
                    'requested_at' => optional($request->created_at)->toIso8601String(),
                ];
            })->filter(fn($item) => !empty($item['id']))->values();

        $outgoingRequests = CustomerFriendship::query()
            ->where('requester_id', $user->id)
            ->where('status', 'pending')
            ->latest('created_at')
            ->get()
            ->map(function ($request) {
                $target = Pelanggan::find($request->addressee_id);
                return [
                    'friendship_id' => $request->id,
                    'id' => $target?->id,
                    'name' => $target?->nama_lengkap ?? 'Pelanggan',
                    'nomer_id' => $target?->nomer_id,
                    'requested_at' => optional($request->created_at)->toIso8601String(),
                ];
            })->filter(fn($item) => !empty($item['id']))->values();

        return response()->json([
            'friends' => $friendList,
            'incoming_requests' => $incomingRequests,
            'outgoing_requests' => $outgoingRequests,
        ]);
    }

    public function searchCustomerFriends(Request $request)
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'query' => 'required|string|min:2|max:100',
        ]);

        $query = trim($data['query']);

        $results = Pelanggan::query()
            ->where('id', '!=', $user->id)
            ->where(function ($builder) use ($query) {
                $builder->where('nama_lengkap', 'like', "%{$query}%")
                    ->orWhere('nomer_id', 'like', "%{$query}%")
                    ->orWhere('no_whatsapp', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get()
            ->map(function ($target) use ($user) {
                $status = CustomerFriendship::betweenUsers($user->id, $target->id)?->status;
                return [
                    'id' => $target->id,
                    'name' => $target->nama_lengkap ?? 'Pelanggan',
                    'nomer_id' => $target->nomer_id,
                    'status' => $status ?? 'none',
                ];
            });

        return response()->json($results);
    }

    public function sendFriendRequest(Request $request)
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'target_id' => ['required', 'string', Rule::exists('pelanggans', 'id')],
        ]);

        $targetId = $data['target_id'];
        if ((string) $targetId === (string) $user->id) {
            return response()->json(['error' => 'Tidak bisa menambahkan diri sendiri'], 422);
        }

        $existing = CustomerFriendship::betweenUsers($user->id, $targetId);

        if ($existing) {
            if ($existing->status === 'accepted') {
                return response()->json(['error' => 'Sudah menjadi teman'], 422);
            }

            if ($existing->status === 'pending') {
                if ((string) $existing->requester_id === (string) $user->id) {
                    return response()->json(['error' => 'Permintaan sudah dikirim'], 422);
                }

                $existing->update([
                    'status' => 'accepted',
                    'accepted_at' => now(),
                ]);

                return response()->json(['success' => true, 'message' => 'Permintaan diterima otomatis']);
            }

            if ($existing->status === 'rejected') {
                $existing->update([
                    'requester_id' => $user->id,
                    'addressee_id' => $targetId,
                    'status' => 'pending',
                    'accepted_at' => null,
                ]);

                return response()->json(['success' => true, 'message' => 'Permintaan pertemanan dikirim ulang']);
            }
        }

        CustomerFriendship::createPending($user->id, $targetId);

        return response()->json(['success' => true, 'message' => 'Permintaan pertemanan dikirim']);
    }

    public function acceptFriendRequest($friendshipId)
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $friendship = CustomerFriendship::find($friendshipId);
        if (!$friendship || $friendship->status !== 'pending') {
            return response()->json(['error' => 'Permintaan tidak ditemukan'], 404);
        }

        if ((string) $friendship->addressee_id !== (string) $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $friendship->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function rejectFriendRequest($friendshipId)
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $friendship = CustomerFriendship::find($friendshipId);
        if (!$friendship || $friendship->status !== 'pending') {
            return response()->json(['error' => 'Permintaan tidak ditemukan'], 404);
        }

        if ((string) $friendship->addressee_id !== (string) $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $friendship->update([
            'status' => 'rejected',
            'accepted_at' => null,
        ]);

        return response()->json(['success' => true]);
    }

    public function getCustomerFriendMessages($friendId)
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $friendship = CustomerFriendship::acceptedBetweenUsers($user->id, $friendId);
        if (!$friendship) {
            return response()->json(['error' => 'Belum berteman'], 403);
        }

        $messages = CustomerFriendMessage::query()
            ->betweenUsers($user->id, $friendId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function sendCustomerFriendMessage(Request $request, $friendId)
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $friendship = CustomerFriendship::acceptedBetweenUsers($user->id, $friendId);
        if (!$friendship) {
            return response()->json(['error' => 'Belum berteman'], 403);
        }

        $data = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = CustomerFriendMessage::create([
            'sender_id' => $user->id,
            'receiver_id' => $friendId,
            'message' => trim($data['message']),
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 201);
    }

    public function markCustomerFriendMessagesRead($friendId)
    {
        $user = Auth::guard('customer')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $friendship = CustomerFriendship::acceptedBetweenUsers($user->id, $friendId);
        if (!$friendship) {
            return response()->json(['error' => 'Belum berteman'], 403);
        }

        $count = CustomerFriendMessage::query()
            ->where('sender_id', $friendId)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'marked_count' => $count]);
    }
}
