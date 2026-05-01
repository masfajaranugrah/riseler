use App\Exports\PelangganExport;
use Maatwebsite\Excel\Facades\Excel;

public function exportExcel()
{
    return Excel::download(
        new PelangganExport,
        'data-pelanggan.xlsx'
    );
}
