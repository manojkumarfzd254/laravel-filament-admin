<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Testing\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\Enums\Format;
use Illuminate\Support\Str;
use Dompdf\Options;
use Dompdf\Dompdf;

class GenerateInvoicePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Ensure directory exists
        $outputDir = storage_path('app/public/invoices');
        // File::ensureDirectoryExists($outputDir);

        // Generate unique PDF filename
        $pdfName = Str::uuid() . '.pdf';

        // Load the HTML for the PDF
        $html = view('pdf.invoices.invoice', ['order' => $this->order])->render();

        // Configure Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF
        $dompdf->render();

        // Save the generated PDF to storage
        file_put_contents($outputDir . '/' . $pdfName, $dompdf->output());

        // Update the order with the invoice path
        $this->order->update(['invoice_path' => 'invoices/' . $pdfName]);
    }
}
