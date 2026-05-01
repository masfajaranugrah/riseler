<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;

class EncryptExistingKtpData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ktp:encrypt-existing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypts existing plaintext KTP numbers and images in the Pelanggans table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting KTP data encryption migration...');

        $pelanggans = Pelanggan::all();
        $countNoKtp = 0;
        $countFotoKtp = 0;

        foreach ($pelanggans as $pelanggan) {
            $updated = false;

            // 1. Encrypt no_ktp
            if (!empty($pelanggan->no_ktp)) {
                // Check if already encrypted (Laravel encrypt produces long base64 string starting with eyJ)
                try {
                    Crypt::decryptString($pelanggan->no_ktp);
                    // If it doesn't throw exception, it's already encrypted
                } catch (DecryptException $e) {
                    // It's plaintext, encrypt it!
                    $encryptedNoKtp = Crypt::encryptString($pelanggan->no_ktp);
                    // Use DB facade to bypass Model cast (if we already added it)
                    DB::table('pelanggans')
                        ->where('id', $pelanggan->id)
                        ->update(['no_ktp' => $encryptedNoKtp]);
                    $countNoKtp++;
                    $updated = true;
                }
            }

            // 2. Encrypt foto_ktp
            if (!empty($pelanggan->foto_ktp) && !Str::endsWith($pelanggan->foto_ktp, '.dat')) {
                // Check if file exists in public disk
                if (Storage::disk('public')->exists($pelanggan->foto_ktp)) {
                    $fileContent = Storage::disk('public')->get($pelanggan->foto_ktp);
                    
                    // Encrypt content
                    $encryptedContent = Crypt::encryptString($fileContent);
                    
                    // Generate new path in private storage
                    $newPath = 'pelanggan/ktp/' . uniqid() . '.dat';
                    
                    // Store encrypted file in local (private) disk
                    Storage::put($newPath, $encryptedContent);
                    
                    // Update database
                    DB::table('pelanggans')
                        ->where('id', $pelanggan->id)
                        ->update(['foto_ktp' => $newPath]);
                        
                    // Delete old plaintext file from public disk
                    Storage::disk('public')->delete($pelanggan->foto_ktp);
                    
                    $countFotoKtp++;
                    $updated = true;
                } else {
                    $this->warn("File not found for pelanggan ID {$pelanggan->id}: {$pelanggan->foto_ktp}");
                }
            }

            if ($updated) {
                $this->info("Migrated KTP data for Pelanggan: {$pelanggan->nama_lengkap} ({$pelanggan->id})");
            }
        }

        $this->info("Migration completed successfully!");
        $this->info("- Encrypted {$countNoKtp} NIKs");
        $this->info("- Encrypted {$countFotoKtp} KTP Photos");
    }
}
