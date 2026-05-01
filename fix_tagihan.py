import re

file_path = '/Users/fajaranugrah/Documents/backup-bil/resources/views/content/apps/Customer/tagihan/tagihan.blade.php'

with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

target = r"""                            <p class="period-label">
                                Periode: {{ \Carbon\Carbon::parse($tagihan->tanggal_mulai)->translatedFormat('F Y') }}
                            </p>
                            <div class="price-amount">
                                Rp {{ number_format($paket->harga ?? 0, 0, ',', '.') }}
                            </div>

                            <p class="price-text">
                                {{ ucwords(\NumberFormatter::create('id_ID',
                                \NumberFormatter::SPELLOUT)->format($paket->harga ?? 0)) }} rupiah
                            </p>"""

replacement = r"""                            <p class="period-label mb-1">
                                Periode: {{ \Carbon\Carbon::parse($tagihan->tanggal_mulai)->translatedFormat('F Y') }}
                            </p>
                            <div class="price-amount mb-2">
                                Rp {{ number_format($paket->harga ?? 0, 0, ',', '.') }}
                            </div>

                            <p class="price-text mb-3">
                                {{ ucwords(\NumberFormatter::create('id_ID',
                                \NumberFormatter::SPELLOUT)->format($paket->harga ?? 0)) }} rupiah
                            </p>

                            <div style="margin-top: 12px;">
                                <span style="background: #f8fafc; color: #475569; padding: 6px 14px; border-radius: 100px; font-size: 0.8125rem; font-weight: 600; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="bi bi-calendar-event"></i> 
                                    Jatuh Tempo: <span style="color: #0f172a;">{{ \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->translatedFormat('d F Y') }}</span>
                                </span>
                            </div>"""

new_content = content.replace(target, replacement)

if new_content != content:
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Success")
else:
    print("Failed to match")
