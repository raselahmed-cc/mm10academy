(function () {
    var prefetched = new Set();
    var activeLoadToken = 0;
    var activeLoadTimeout = null;

    function setLoading(modal, isLoading) {
        if (!modal) {
            return;
        }

        var frame = modal.querySelector('.mm10-player-modal__frame');
        modal.classList.toggle('is-loading', !!isLoading);

        if (frame) {
            frame.style.visibility = isLoading ? 'hidden' : 'visible';
        }
    }

    function clearLoadTimeout() {
        if (activeLoadTimeout) {
            clearTimeout(activeLoadTimeout);
            activeLoadTimeout = null;
        }
    }

    function armLoadTimeout(modal, frame, token) {
        clearLoadTimeout();

        activeLoadTimeout = setTimeout(function () {
            if (!modal || !frame || token !== activeLoadToken) {
                return;
            }

            // Fail-safe: never leave users on a perpetual loader.
            setLoading(modal, false);
        }, 9000);
    }

    function warmProfileUrl(url) {
        if (!url || prefetched.has(url)) {
            return;
        }

        prefetched.add(url);

        // Warm browser cache so modal iframe loads faster on click.
        fetch(url, {
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).catch(function () {
            // Ignore prefetch failures; normal modal load still works.
        });
    }

    function trimEmbeddedPage(frame) {
        if (!frame || !frame.contentDocument) {
            return;
        }

        var doc = frame.contentDocument;
        var css = [
            '#wpadminbar,',
            '#masthead,',
            '#colophon,',
            '.site-header,',
            '.site-footer,',
            '.ast-above-header-wrap,',
            '.ast-below-header-wrap,',
            '.ast-mobile-header-wrap,',
            '.site-primary-header-wrap,',
            '.ast-theme-transparent-header #masthead,',
            '.ast-builder-menu-mobile,',
            '.main-header-menu-toggle,',
            '.entry-meta,',
            '.byline,',
            '.posted-on { display: none !important; }',
            'html, body { margin: 0 !important; padding: 0 !important; background: #fff !important; }',
            '#content, .site-content { margin-top: 0 !important; padding-top: 0 !important; }',
            '.ast-container, .site-content .ast-container { max-width: 100% !important; padding-left: 12px !important; padding-right: 12px !important; }',
            '#primary, .content-area, .ast-article-single { margin: 0 !important; }'
        ].join(' ');

        var style = doc.getElementById('mm10-modal-embedded-cleanup');
        if (!style) {
            style = doc.createElement('style');
            style.id = 'mm10-modal-embedded-cleanup';
            doc.head.appendChild(style);
        }
        style.textContent = css;
    }

    function buildModal() {
        var existing = document.getElementById('mm10-player-modal');
        if (existing) {
            return existing;
        }

        var modal = document.createElement('div');
        modal.id = 'mm10-player-modal';
        modal.className = 'mm10-player-modal';
        modal.setAttribute('aria-hidden', 'true');

        modal.innerHTML = '' +
            '<div class="mm10-player-modal__backdrop" data-modal-close="1"></div>' +
            '<div class="mm10-player-modal__dialog" role="dialog" aria-modal="true" aria-label="Player Profile Preview">' +
            '  <div class="mm10-player-modal__header">' +
            '    <h3 class="mm10-player-modal__title">Player Profile</h3>' +
            '    <button type="button" class="mm10-player-modal__close" data-modal-close="1" aria-label="Close">&times;</button>' +
            '  </div>' +
            '  <div class="mm10-player-modal__body">' +
            '    <div class="mm10-player-modal__loading"><span class="mm10-player-modal__spinner"></span><span>Loading profile...</span></div>' +
            '    <iframe class="mm10-player-modal__frame" title="Player Profile"></iframe>' +
            '  </div>' +
            '</div>';

        document.body.appendChild(modal);

        var frame = modal.querySelector('.mm10-player-modal__frame');
        if (frame) {
            frame.addEventListener('load', function () {
                if (frame.src && frame.src.indexOf('about:blank') !== -1) {
                    return;
                }

                clearLoadTimeout();

                try {
                    trimEmbeddedPage(frame);
                } catch (error) {
                    // If cross-origin or inaccessible, keep default rendering.
                }

                frame.dataset.ready = '1';
                setLoading(modal, false);
            });

            frame.addEventListener('error', function () {
                clearLoadTimeout();
                setLoading(modal, false);
            });
        }

        return modal;
    }

    function openModal(url, titleText) {
        var modal = buildModal();
        var frame = modal.querySelector('.mm10-player-modal__frame');
        var title = modal.querySelector('.mm10-player-modal__title');

        var isSameLoadedUrl = frame && frame.dataset.currentUrl === url && frame.dataset.ready === '1';
        if (!isSameLoadedUrl) {
            activeLoadToken += 1;
            setLoading(modal, true);
            frame.dataset.ready = '0';
            frame.dataset.currentUrl = url;
            frame.src = url;
            armLoadTimeout(modal, frame, activeLoadToken);
        } else {
            clearLoadTimeout();
            setLoading(modal, false);
        }

        title.textContent = titleText ? titleText + ' Profile' : 'Player Profile';
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('mm10-modal-open');
    }

    function closeModal() {
        var modal = document.getElementById('mm10-player-modal');
        if (!modal) {
            return;
        }

        var frame = modal.querySelector('.mm10-player-modal__frame');
        // Keep iframe source in memory to make reopening instant.
        if (frame) {
            frame.blur();
        }

        clearLoadTimeout();

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('mm10-modal-open');
    }

    document.addEventListener('click', function (event) {
        var trigger = event.target.closest('.mm10-player-modal-trigger');
        if (trigger) {
            event.preventDefault();
            openModal(trigger.href, trigger.getAttribute('data-player-name') || 'Player');
            return;
        }

        if (event.target.closest('[data-modal-close="1"]')) {
            closeModal();
        }
    });

    document.addEventListener('mouseover', function (event) {
        var trigger = event.target.closest('.mm10-player-modal-trigger');
        if (!trigger) {
            return;
        }
        warmProfileUrl(trigger.href);
    });

    document.addEventListener('focusin', function (event) {
        var trigger = event.target.closest('.mm10-player-modal-trigger');
        if (!trigger) {
            return;
        }
        warmProfileUrl(trigger.href);
    });

    document.addEventListener('touchstart', function (event) {
        var trigger = event.target.closest('.mm10-player-modal-trigger');
        if (!trigger) {
            return;
        }
        warmProfileUrl(trigger.href);
    }, { passive: true });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
})();
