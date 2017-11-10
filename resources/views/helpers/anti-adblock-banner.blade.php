<div class="container">
    <div id="please-im-begging-you" class="hidden" aria-hidden="true">
        <img src="/images/adblock-logo.png" alt="Please disable your adblocker for this website">
        <span>
            <strong>Could you disable adblock for this website?</strong>
            I've put a lot of hours into making these tools, I would appreciate it if you could support me by disabling your adblocker
        </span>
    </div>
</div>

@push('inline-footer-scripts')
    <script>
        function adBlockDetected() {
            document.getElementById('please-im-begging-you').classList.remove("hidden");
        }

        if(typeof blockAdBlock === 'undefined') {
            adBlockDetected();
        }
        else {
            blockAdBlock.onDetected(adBlockDetected);
        }
    </script>
@endpush
