<script>
(() => {
    const init = () => {
        const modal = document.getElementById('seoHelpModal');
        if (!modal || modal.dataset.initialized === '1') return;
        modal.dataset.initialized = '1';

        const fields = {
            google_verification: {
                kicker: 'GOOGLE SEARCH CONSOLE',
                title: 'Google Search Console verification',
                intro: 'Verify ownership of fuelfreepowerplant.com with Google’s HTML-tag method.',
                url: 'https://search.google.com/search-console',
                steps: [
                    'Open Google Search Console and sign in with the Google account that should manage this site.',
                    'Choose Add property and add fuelfreepowerplant.com.',
                    'Select the HTML tag verification method.',
                    'Copy only the value inside content="..." from the google-site-verification tag.',
                    'Paste that value into this field and click Save SEO settings.',
                    'Return to Search Console and click Verify. If it fails, check the live homepage source for the verification tag.'
                ],
                tip: 'Google requires the verification meta tag to be inside the public page <head>. This system inserts the saved value there automatically.'
            },
            bing_verification: {
                kicker: 'BING WEBMASTER',
                title: 'Bing Webmaster verification',
                intro: 'Add the website to Bing Webmaster Tools and verify it with Bing’s HTML meta-tag method.',
                url: 'https://www.bing.com/webmasters/',
                steps: [
                    'Open Bing Webmaster Tools and sign in with your Microsoft account.',
                    'Add or select fuelfreepowerplant.com.',
                    'Open the verification options and choose the HTML meta-tag method.',
                    'Copy the verification value supplied by Bing, not the whole HTML tag.',
                    'Paste the value into this field and click Save SEO settings.',
                    'Return to Bing Webmaster Tools and complete verification.'
                ],
                tip: 'Only the verification value belongs in this field. Do not paste a complete <meta> element.'
            },
            meta_verification: {
                kicker: 'META BUSINESS',
                title: 'Meta domain verification',
                intro: 'Verify the website domain in Meta Business when your organization uses Meta/Facebook business assets.',
                url: 'https://business.facebook.com/',
                steps: [
                    'Open Meta Business and sign in with the business account that owns the domain.',
                    'Open Business Settings and find Domains under your business assets.',
                    'Add or select fuelfreepowerplant.com.',
                    'Choose the meta-tag verification method when Meta offers it.',
                    'Copy only the verification value, paste it here, and click Save SEO settings.',
                    'Return to Meta Business and complete the verification step.'
                ],
                tip: 'If Meta offers DNS or HTML-file verification instead, use that method in Meta; this field is specifically for the meta-tag value.'
            },
            ga4_measurement_id: {
                kicker: 'GOOGLE ANALYTICS 4',
                title: 'GA4 Measurement ID',
                intro: 'Connect this website to the correct GA4 web data stream without editing website code.',
                url: 'https://analytics.google.com/',
                steps: [
                    'Open Google Analytics and select the correct account and GA4 property.',
                    'Go to Admin → Data collection and modification → Data streams.',
                    'Open the Web data stream for fuelfreepowerplant.com.',
                    'Find Measurement ID in Stream details. It normally starts with G-.',
                    'Copy the complete ID, paste it here, and click Save SEO settings.',
                    'Open the GA4 Realtime report after visiting the website to confirm data is arriving.'
                ],
                tip: 'Google documents the Measurement ID as the identifier shown in the web data stream details, normally in the G-XXXXXXXXXX format.'
            },
            indexnow_key: {
                kicker: 'INDEXNOW',
                title: 'IndexNow API key',
                intro: 'Create a website-specific IndexNow key and let this site expose its verification file automatically.',
                url: 'https://www.indexnow.org/documentation',
                steps: [
                    'Open the IndexNow documentation and generate a unique key for this website.',
                    'Use 8–128 hexadecimal characters (0–9 and A–F/a–f) with hyphens if desired.',
                    'Paste the key here and click Save SEO settings.',
                    'The site will expose the key at /indexnow/{key}.txt for verification.',
                    'Open that URL in a private browser window and confirm it returns only the exact key.',
                    'Use the same key when submitting supported URL updates to an IndexNow endpoint.'
                ],
                tip: 'IndexNow requires a publicly reachable key file whose contents exactly match the key used for submission.'
            }
        };

        const byId = (id) => document.getElementById(id);
        const open = (topic) => {
            const guide = fields[topic];
            if (!guide) return;
            byId('seoHelpKicker').textContent = guide.kicker;
            byId('seoHelpTitle').textContent = guide.title;
            byId('seoHelpIntro').textContent = guide.intro;
            byId('seoHelpSteps').innerHTML = guide.steps.map((step) => `<li>${step}</li>`).join('');
            byId('seoHelpTip').textContent = guide.tip;
            byId('seoHelpOfficial').href = guide.url;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('seo-modal-open');
            byId('seoHelpTitle')?.focus?.();
        };
        const close = () => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('seo-modal-open');
        };

        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-help-topic]');
            if (trigger) {
                event.preventDefault();
                event.stopPropagation();
                open(trigger.dataset.helpTopic);
                return;
            }
            if (event.target.closest('[data-help-close]')) {
                event.preventDefault();
                close();
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.classList.contains('is-open')) close();
        });
    };

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
    else init();
})();
</script>
