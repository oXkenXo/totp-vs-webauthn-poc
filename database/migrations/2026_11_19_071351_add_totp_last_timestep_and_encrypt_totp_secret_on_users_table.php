<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('totp_last_timestep')->nullable()->after('totp_enabled');
        });

        // Existing secrets were stored in plain text; the model now casts them as encrypted.
        $this->rewriteSecrets(fn (string $secret): ?string => $this->isEncrypted($secret) ? null : Crypt::encryptString($secret));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->rewriteSecrets(fn (string $secret): ?string => $this->isEncrypted($secret) ? Crypt::decryptString($secret) : null);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('totp_last_timestep');
        });
    }

    /**
     * @param  callable(string): ?string  $transform  Returns the new value, or null to leave the row as is.
     */
    private function rewriteSecrets(callable $transform): void
    {
        DB::table('users')->whereNotNull('totp_secret')->orderBy('id')->each(function (object $user) use ($transform): void {
            $newSecret = $transform($user->totp_secret);

            if ($newSecret !== null) {
                DB::table('users')->where('id', $user->id)->update(['totp_secret' => $newSecret]);
            }
        });
    }

    private function isEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);

            return true;
        } catch (DecryptException) {
            return false;
        }
    }
};
