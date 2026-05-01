

<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (string) $user->id === (string) $id;
});

// Chat private channel - support UUID dan multiple guards
Broadcast::channel('chat.{userId}', function ($user, $userId) {
    $webUserId = Auth::guard('web')->user()?->id;
    $customerUserId = Auth::guard('customer')->user()?->id;
    $webRole = Auth::guard('web')->user()?->role;
    $isCsAdmin = in_array($webRole, ['administrator', 'admin', 'customer_service'], true);

    // Support dual-login in same browser (web + customer session at once).
    // Admin/CS can subscribe any CS chat channel for inbox/dashboard usage.
    $authorized = $isCsAdmin
        || (string) $webUserId === (string) $userId
        || (string) $customerUserId === (string) $userId;
    
    Log::info('Broadcasting Auth Check - Chat Channel', [
        'user_id' => $user->id,
        'web_user_id' => $webUserId,
        'customer_user_id' => $customerUserId,
        'web_role' => $webRole,
        'is_cs_admin' => $isCsAdmin,
        'channel_user_id' => $userId,
        'authorized' => $authorized,
        'user_type' => get_class($user)
    ]);
    
    return $authorized;
}, ['guards' => ['web', 'customer']]); 

Broadcast::channel('billing-chat.{userId}', function ($user, $userId) {
    $webUserId = Auth::guard('web')->user()?->id;
    $customerUserId = Auth::guard('customer')->user()?->id;
    $webRole = Auth::guard('web')->user()?->role;
    $isBillingAdmin = in_array($webRole, ['administrator', 'admin'], true);

    // Support dual-login in same browser (web + customer session at once).
    // Billing admin can subscribe billing channels for dashboard monitoring.
    $authorized = $isBillingAdmin
        || (string) $webUserId === (string) $userId
        || (string) $customerUserId === (string) $userId;

    Log::info('Broadcasting Auth Check - Billing Chat Channel', [
        'user_id' => $user->id,
        'web_user_id' => $webUserId,
        'customer_user_id' => $customerUserId,
        'web_role' => $webRole,
        'is_billing_admin' => $isBillingAdmin,
        'channel_user_id' => $userId,
        'authorized' => $authorized,
        'user_type' => get_class($user)
    ]);

    return $authorized;
}, ['guards' => ['web', 'customer']]);

// Admin inbox channel - untuk semua admin menerima pesan pelanggan secara real-time
Broadcast::channel('admin-inbox', function ($user) {
    // Hanya admin yang bisa subscribe ke channel ini
    $isAdmin = isset($user->role) && in_array($user->role, ['administrator', 'admin', 'customer_service']);
    
    Log::info('Broadcasting Auth Check - Admin Inbox Channel', [
        'user_id' => $user->id,
        'user_role' => $user->role ?? 'unknown',
        'authorized' => $isAdmin,
        'user_type' => get_class($user)
    ]);
    
    return $isAdmin;
}, ['guards' => ['web']]); 
