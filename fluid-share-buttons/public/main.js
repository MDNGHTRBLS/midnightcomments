jQuery(document).ready(function($) {
    const $masterWrapper = $('.fsb-master-wrapper');
    if ($masterWrapper.length === 0) return;

    const $fabContainer = $('.fsb-fab-container');
    const $mainFab = $('.fsb-main-fab');
    const $scrollTopBtn = $('.fsb-scroll-top-btn');
    const $progressCircle = $('.fsb-scroll-progress');
    const $body = $('body');
    const $window = $(window);
    const postID = $masterWrapper.data('postid');

    let menuVisibilityRuleFired = (fsb_settings.visibilityRule !== 'scroll');
    let scrollTopVisible = false;

    // --- Master Scroll Handler ---
    function masterScrollHandler() {
        const scrollTop = $window.scrollTop();
        const docHeight = $(document).height() - $window.height();
        const scrollPercent = (docHeight > 0) ? (scrollTop / docHeight) * 100 : 0;
        
        // Visibility Rule for the main share button container
        if (!menuVisibilityRuleFired) {
            if (scrollPercent >= parseFloat(fsb_settings.scrollPercentage)) {
                $fabContainer.removeClass('fsb-hidden-by-rule');
                menuVisibilityRuleFired = true;
            }
        }

        // Visibility for Scroll to Top button & Animation Trigger
        if (scrollPercent >= parseFloat(fsb_settings.scrollTopPercentage)) {
            if (!scrollTopVisible) {
                $scrollTopBtn.removeClass('fsb-scroll-hidden');
                $fabContainer.addClass('fsb-pushed-up');
                scrollTopVisible = true;
            }
        } else {
            if (scrollTopVisible) {
                $scrollTopBtn.addClass('fsb-scroll-hidden');
                $fabContainer.removeClass('fsb-pushed-up');
                scrollTopVisible = false;
            }
        }

        // Scroll Progress Ring Update
        if ($progressCircle.length > 0) {
            const scrollFraction = (docHeight > 0) ? (scrollTop / docHeight) : 0;
            const circle = $progressCircle[0];
            const circumference = 100;
            const offset = circumference - (scrollFraction * circumference);
            circle.style.strokeDashoffset = Math.max(0, Math.min(offset, 100));
        }
    }

    // --- Fetch Views via REST API ---
    function fetchViews() {
        const $shareCounter = $('.fsb-share-number');
        if ($shareCounter.length === 0 || !postID) return;
        
        const restURL = fsb_settings.rest_url + 'fsb/v1/views/' + postID;
        $.ajax({
            url: restURL,
            method: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', fsb_settings.nonce);
            },
            success: function(response) {
                if(response.views) {
                    $shareCounter.text(response.views);
                }
            }
        });
    }

    // --- Initialize Logic on Page Load ---
    if (fsb_settings.visibilityRule === 'always') {
        $fabContainer.removeClass('fsb-hidden-by-rule');
    }
    $window.on('scroll', masterScrollHandler);
    setTimeout(masterScrollHandler, 100);
    fetchViews();
    
    // --- Event Handlers ---
    $scrollTopBtn.on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 500);
    });

    $mainFab.on('click', function() {
        $fabContainer.toggleClass('expanded');
    });

    $body.on('click', function(e) {
        if ($fabContainer.hasClass('expanded') && !$(e.target).closest('.fsb-fab-container').length) {
            $fabContainer.removeClass('expanded');
        }
    });

    $fabContainer.on('click', '.fsb-share-btn', function(e) {
        const $button = $(this);
        trackClick($button);
        
        if ($button.hasClass('fsb-copy-link')) {
            e.preventDefault();
            navigator.clipboard.writeText($button.data('link')).then(() => {
                if (!$button.hasClass('copied')) {
                    $button.addClass('copied');
                    setTimeout(() => $button.removeClass('copied'), 2000);
                }
            }).catch(err => console.error('Could not copy text: ', err));
        }
    });
    
    // --- Click Tracking ---
    function trackClick($button) {
        const network = $button.data('network');
        $.ajax({
            url: fsb_settings.ajax_url,
            type: 'POST',
            data: {
                action: 'fsb_track_click',
                nonce: fsb_settings.ajax_nonce,
                network: network
            },
            error: (j, t, e) => console.error('FSB AJAX Error:', t, e)
        });
    }
});
