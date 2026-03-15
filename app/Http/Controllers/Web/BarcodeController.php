<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BarcodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BarcodeController extends Controller
{
    public function __construct(
        private readonly BarcodeService $barcodes
    ) {
    }

    public function index(): View
    {
        return view('barcodes.index', [
            'barcodes' => $this->barcodes->list(10),
        ]);
    }

    public function create(): View
    {
        return view('barcodes.create', [
            'employees' => $this->barcodes->employees(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
        ]);

        $this->barcodes->generateForEmployee($data['employee_id'], $request);

        return redirect()->route('barcodes.index')->with('success', 'Barcode generated successfully.');
    }

    public function show(string $id): View
    {
        $barcode = $this->barcodes->detail($id);
        abort_if($barcode === null, 404);

        return view('barcodes.show', [
            'barcode' => $barcode,
            'qrBase64' => $this->barcodes->qrPngBase64($barcode->code),
        ]);
    }

    public function download(string $id): Response
    {
        $barcode = $this->barcodes->detail($id);
        abort_if($barcode === null, 404);

        $content = $this->barcodes->qrPngBinary($barcode->code);

        return response($content, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="barcode-'.$barcode->code.'.png"',
        ]);
    }

    public function regenerate(Request $request, string $id): RedirectResponse
    {
        $barcode = $this->barcodes->regenerate($id, $request);

        if (! $barcode) {
            abort(404);
        }

        return redirect()->route('barcodes.show', $barcode->id)->with('success', 'Barcode regenerated successfully.');
    }

    public function deactivate(Request $request, string $id): RedirectResponse
    {
        $this->barcodes->deactivate($id, $request);

        return redirect()->route('barcodes.index')->with('success', 'Barcode deactivated successfully.');
    }
}
