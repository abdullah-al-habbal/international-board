{{-- resources/views/web/certifications/partials/certificate_actions.blade.php --}}
@php
    $certificateUrl = route('web.certifications.show', $certification->accreditation_number);
@endphp

<div class="certificate-actions">
    <button type="button" class="btn btn-certificate btn-certificate-primary js-cert-print">
        <i class="tf-ion-ios-printer-outline" aria-hidden="true"></i>
        <span>{{ __('web.buttons.print') }}</span>
    </button>
    <button type="button" class="btn btn-certificate btn-certificate-outline js-cert-pdf">
        <i class="tf-ion-ios-download-outline" aria-hidden="true"></i>
        <span>{{ __('web.buttons.save_pdf') }}</span>
    </button>
    <button type="button" class="btn btn-certificate btn-certificate-outline js-cert-share">
        <i class="tf-ion-ios-share-outline" aria-hidden="true"></i>
        <span>{{ __('web.buttons.share') }}</span>
    </button>
    <button type="button" class="btn btn-certificate btn-certificate-outline js-cert-copy">
        <i class="tf-ion-ios-link-outline" aria-hidden="true"></i>
        <span>{{ __('web.buttons.copy_link') }}</span>
    </button>
</div>

<div class="certificate-copy-status visually-hidden" role="status" aria-live="polite"></div>

@push('scripts')
<script>
(function () {
    var url = {{ Js::from($certificateUrl) }};
    var status = document.querySelector('.certificate-copy-status');
    var copiedMessage = {{ Js::from(__('web.labels.link_copied')) }};

    function announce(message) {
        if (status) {
            status.textContent = message;
        }
    }

    function printCertificate() {
        window.print();
    }

    document.querySelectorAll('.js-cert-print, .js-cert-pdf').forEach(function (button) {
        button.addEventListener('click', printCertificate);
    });

    function copyLink() {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(function () {
                announce(copiedMessage);
            }, function () {
                legacyCopy();
            });
        } else {
            legacyCopy();
        }
    }

    function legacyCopy() {
        var input = document.createElement('input');
        input.value = url;
        input.style.position = 'fixed';
        input.style.opacity = '0';
        document.body.appendChild(input);
        input.select();
        try {
            document.execCommand('copy');
            announce(copiedMessage);
        } catch (e) {
            window.prompt('', url);
        }
        document.body.removeChild(input);
    }

    var shareButton = document.querySelector('.js-cert-share');
    if (shareButton) {
        shareButton.addEventListener('click', function () {
            if (navigator.share) {
                navigator.share({ title: document.title, url: url })
                    .catch(function (err) {
                        if (err && err.name !== 'AbortError') {
                            copyLink();
                        }
                    });
            } else {
                copyLink();
            }
        });
    }

    var copyButton = document.querySelector('.js-cert-copy');
    if (copyButton) {
        copyButton.addEventListener('click', copyLink);
    }
})();
</script>
@endpush
