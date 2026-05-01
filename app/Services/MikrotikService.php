<?php

namespace App\Services;

use App\Models\Router;
use Exception;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Query;

class MikrotikService
{
    protected $client;

    /**
     * Connect to the MikroTik router.
     */
    public function connect(Router $router)
    {
        try {
            $this->client = new Client([
                'host' => $router->ip_address,
                'user' => $router->username,
                'pass' => $router->password,
                'port' => (int) $router->port,
                'timeout' => 10,
                'socket_timeout' => 10,
            ]);
            return true;
        } catch (Exception $e) {
            Log::error('MikroTik Connection Check Failed: ' . $e->getMessage());
            throw new Exception('Gagal terhubung ke MikroTik: ' . $e->getMessage());
        }
    }

    /**
     * Get system identity name.
     */
    public function getIdentity()
    {
        if (!$this->client) {
            throw new Exception('Client not connected.');
        }

        $query = new Query('/system/identity/print');
        $response = $this->client->query($query)->read();

        return $response[0]['name'] ?? 'Unknown';
    }

    /**
     * Block/Isolate a customer (PPP Secret or Queue).
     * This implementation assumes PPP Secret modification for isolation.
     * You can adapt this to firewall address lists or simple queues.
     */
    public function isolateCustomer($username, $profileIsolir = 'isolir')
    {
        if (!$this->client) {
            throw new Exception('Client not connected.');
        }

        try {
            // Find PPP Secret
            $query = new Query('/ppp/secret/print');
            $query->where('name', $username);
            $secrets = $this->client->query($query)->read();

            if (count($secrets) > 0) {
                // Update profile to isolir
                $secretId = $secrets[0]['.id'];
                
                $updateQuery = new Query('/ppp/secret/set');
                $updateQuery->equal('.id', $secretId);
                $updateQuery->equal('profile', $profileIsolir);
                
                $this->client->query($updateQuery)->read();
                
                // Kill active connection to force reconnect with new profile
                $this->killActiveConnection($username);
                
                return true;
            }
            
            return false; // User not found
            
        } catch (Exception $e) {
            Log::error('Failed to isolate customer: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Restore customer service (Restore original profile).
     */
    public function restoreCustomer($username, $originalProfile)
    {
        if (!$this->client) {
            throw new Exception('Client not connected.');
        }

        try {
             // Find PPP Secret
            $query = new Query('/ppp/secret/print');
            $query->where('name', $username);
            $secrets = $this->client->query($query)->read();

            if (count($secrets) > 0) {
                // Update profile back to original
                $secretId = $secrets[0]['.id'];
                
                $updateQuery = new Query('/ppp/secret/set');
                $updateQuery->equal('.id', $secretId);
                $updateQuery->equal('profile', $originalProfile);
                
                $this->client->query($updateQuery)->read();

                // Kill active connection to force reconnect
                $this->killActiveConnection($username);
                
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            Log::error('Failed to restore customer: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Kill active PPP connection for a user.
     */
    public function killActiveConnection($username)
    {
        if (!$this->client) {
             return;
        }

        $query = new Query('/ppp/active/print');
        $query->where('name', $username);
        $active = $this->client->query($query)->read();

        if (count($active) > 0) {
            $id = $active[0]['.id'];
            $removeQuery = new Query('/ppp/active/remove');
            $removeQuery->equal('.id', $id);
            $this->client->query($removeQuery)->read();
        }
    }
    
    // Check if user is active/online
    public function isUserOnline($username) {
        if (!$this->client) {
            return false;
        }
        
        $query = new Query('/ppp/active/print');
        $query->where('name', $username);
        $active = $this->client->query($query)->read();
        
        return count($active) > 0;
    }
}
