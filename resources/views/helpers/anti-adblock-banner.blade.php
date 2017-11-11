<div class="container">
    <div id="please-im-begging-you" class="hidden" aria-hidden="true">
        <img src="/images/adblock-logo.png" alt="Please disable your adblocker for this website">
        <span>
            <strong>Could you whitelist this website in your adblocker?</strong>
            I would appreciate it if you could support me by disabling your adblocker, I've put a lot of hours into making these tools
        </span>
    </div>
</div>

@push('inline-footer-scripts')
    <script>
        function adBlockDetected() {
            document.getElementById('please-im-begging-you').classList.remove("hidden");
        }

        (typeof blockAdBlock === 'undefined') ? adBlockDetected() : blockAdBlock.onDetected(adBlockDetected);
    </script>
@endpush
