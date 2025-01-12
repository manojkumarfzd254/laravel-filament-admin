<div style="text-align: center; padding: 10px; border-bottom: 1px solid #ccc;">
    <h1 style="margin: 0;">Your Company Name</h1>
    <p style="margin: 0;">Address, City, State, ZIP</p>
    <p style="margin: 0;">Phone: (123) 456-7890 | Email: contact@yourcompany.com</p>
    <hr style="margin: 10px 0;">
    <table style="width: 100%; text-align: left;">
        <tr>
            <td><strong>Invoice #: </strong> #{{ $order->id }}</td>
            <td><strong>Date: </strong> {{ $order->created_at->format('d-m-Y') }}</td>
        </tr>
    </table>
</div>
