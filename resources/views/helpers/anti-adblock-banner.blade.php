<div id="AntiAdblock" class="container mx-auto px-2 mb-8 hidden">
    <div class="flex max-w-lg" aria-hidden="true">
        <img class="h-10 mr-4" src="/images/adblock-logo.png" alt="Please disable your adblocker for this website">
        <span>
            <strong>Could you whitelist this website in your adblocker?</strong>
            I would appreciate it if you could support me by disabling your adblocker, the ads help pay for the server costs
        </span>
    </div>
</div>

@push('footer')
    <script>
        function adBlockDetected() {
            document.getElementById("AntiAdblock").classList.remove("hidden");
        }

        (typeof blockAdBlock === "undefined") ? adBlockDetected() : blockAdBlock.onDetected(adBlockDetected);
    </script>
@endpush
