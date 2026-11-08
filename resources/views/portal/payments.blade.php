<x-layouts.portal :portal="$portal" title="Payments">
    <x-page-header title="Payments" subtitle="Set the price of a cover page and see what students have paid through Razorpay." />

    <form method="POST" action="{{ route('admin.payments.price') }}" class="card mb-4" novalidate aria-labelledby="price-heading">
        @csrf
        @method('PUT')
        <div class="card-header py-3"><h2 id="price-heading" class="h6 mb-0">Price per cover page</h2></div>
        <div class="card-body">
            @if ($price > 0 && ! $isRazorpayConfigured)
                <div class="alert alert-warning" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-1" aria-hidden="true"></i>
                    Razorpay keys aren't set, so students can't print. Add RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET and RAZORPAY_WEBHOOK_SECRET to .env, or set the price to 0.
                </div>
            @endif
            <div class="row g-3 align-items-end">
                <div class="col-sm-6 col-lg-4">
                    <label class="form-label" for="price">Price (₹)</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text">₹</span>
                        <input id="price" name="price" type="number" min="0" max="10000" step="0.01" inputmode="decimal" @class(['form-control', 'is-invalid' => $errors->has('price')]) value="{{ old('price', rtrim(rtrim(number_format($price / 100, 2, '.', ''), '0'), '.')) }}" required aria-describedby="price_help">
                        <x-field-error for="price">Enter a price, or 0 to make printing free.</x-field-error>
                    </div>
                    <div id="price_help" class="form-text">0 makes printing free. Students pay this each time they print a cover page.</div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end"><button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk me-1" aria-hidden="true"></i> Save price</button></div>
    </form>

    <x-table-card id="payments-table" title="Recent payments" :count="$payments->count()" placeholder="Search payments">
        <thead>
            <tr><th>Student</th><th>Amount</th><th class="text-center">Status</th><th>Payment ID</th><th>Order ID</th><th>When</th></tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr>
                    <td>
                        <span class="fw-semibold d-block">{{ $payment->student->name }}</span>
                        <span class="small text-body-secondary font-monospace">{{ $payment->student->tu_roll }}</span>
                    </td>
                    <td>{{ $payment->formattedAmount() }}</td>
                    <td class="text-center" data-export="{{ $payment->status->label() }}"><span class="badge text-bg-{{ $payment->status->color() }}">{{ $payment->status->label() }}</span></td>
                    <td class="font-monospace small">{{ $payment->razorpay_payment_id ?? '—' }}</td>
                    <td class="font-monospace small">{{ $payment->razorpay_order_id }}</td>
                    <td class="text-body-secondary" data-export="{{ $payment->created_at->toDateTimeString() }}" title="{{ $payment->created_at->format('j M Y, g:i a') }}">{{ $payment->created_at->diffForHumans() }}</td>
                </tr>
            @empty
                <x-empty-state colspan="6" title="No payments yet" icon="fa-indian-rupee-sign" />
            @endforelse
        </tbody>
    </x-table-card>
</x-layouts.portal>
