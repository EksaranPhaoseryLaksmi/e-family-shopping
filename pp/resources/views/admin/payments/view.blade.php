<h2>Payment Detail</h2>

<p><strong>Ref:</strong> {{ $payment->payment_ref }}</p>
<p><strong>Status:</strong> {{ $payment->status }}</p>
<p><strong>Amount:</strong> {{ $payment->amount }}</p>
<p><strong>MD5:</strong> {{ $payment->bakong_md5 }}</p>

<h4>Cart:</h4>
<pre>{{ json_encode(json_decode($payment->cart), JSON_PRETTY_PRINT) }}</pre>
