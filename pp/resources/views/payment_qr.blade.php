<div class="box">
    <h2>KHQR Payment</h2>

    <p>Amount: <strong>${{ number_format($amount, 2) }}</strong></p>

    <img id="qrImg" width="230" style="display:none;">

    <p id="khqrText" style="font-size:12px; word-break:break-all;"></p>

    <div id="status" style="margin-top:10px;">
        ⏳ Generating QR...
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    fetch("/khqr/generate", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            amount: {{ $amount }}
        })
    })
    .then(res => res.json())
    .then(res => {

        console.log("KHQR RESPONSE:", res);

        const qrString = res.data?.qrString;

        if (!qrString) {
            document.getElementById("status").innerHTML =
                "❌ QR not generated";
            return;
        }

        document.getElementById("qrImg").src =
            "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data="
            + encodeURIComponent(qrString);

        document.getElementById("qrImg").style.display = "block";

        document.getElementById("khqrText").innerText = qrString;

        document.getElementById("status").innerText = "Scan to pay 💳";

        window.khqrMd5 = res.data.md5;
    })
    .catch(err => {
        console.error(err);
    });
});
</script>
