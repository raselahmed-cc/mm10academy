(function () {
    if (!document.body.classList.contains('woocommerce-account')) {
        return;
    }

    var accountNav = document.querySelector('.woocommerce-MyAccount-navigation');
    var accountContent = document.querySelector('.woocommerce-MyAccount-content');
    if (!accountNav || !accountContent) {
        return;
    }

    var loading = false;
    var cache = new Map();
    var inflight = new Map();
    var parser = new DOMParser();
    var MAX_CACHE_ENTRIES = 6;
    var routerConfig = window.mm10AccountRouter || {};
    var FRAGMENT_PARAM = routerConfig.fragmentParam || 'mm10_account_fragment';
    var NONCE_PARAM = routerConfig.nonceParam || 'mm10_account_fragment_nonce';
    var NONCE_VALUE = routerConfig.nonce || '';
    var REQUEST_HEADER = routerConfig.requestHeader || 'X-MM10-Account-Fragment';

    function normalizeUrl(inputUrl) {
        var parsed = new URL(inputUrl, window.location.href);
        parsed.hash = '';
        return parsed.toString();
    }

    function cachePut(url, html) {
        if (!html) {
            return;
        }

        if (cache.has(url)) {
            cache.delete(url);
        }
        cache.set(url, html);

        if (cache.size > MAX_CACHE_ENTRIES) {
            var oldestKey = cache.keys().next().value;
            cache.delete(oldestKey);
        }
    }

    function cacheGet(url) {
        if (!cache.has(url)) {
            return null;
        }

        var html = cache.get(url);
        // Touch the key for basic LRU behavior.
        cache.delete(url);
        cache.set(url, html);
        return html;
    }

    function setLoading(state) {
        document.body.classList.toggle('mm10-account-loading', !!state);
    }

    function buildFragmentUrl(rawUrl) {
        var parsed = new URL(rawUrl, window.location.href);
        parsed.searchParams.set(FRAGMENT_PARAM, '1');
        if (NONCE_VALUE) {
            parsed.searchParams.set(NONCE_PARAM, NONCE_VALUE);
        }
        parsed.hash = '';
        return parsed.toString();
    }

    function isMyAccountUrl(url) {
        return /\/my-account\//.test(url.pathname);
    }

    function isLogoutUrl(url) {
        return /customer-logout|action=logout/.test(url.href);
    }

    function shouldHandleClick(anchor, event) {
        if (!anchor) {
            return false;
        }

        if (event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return false;
        }

        if (typeof event.button === 'number' && event.button !== 0) {
            return false;
        }

        if (anchor.target === '_blank' || anchor.hasAttribute('download')) {
            return false;
        }

        var href = anchor.getAttribute('href');
        if (!href || href.indexOf('javascript:') === 0 || href.charAt(0) === '#') {
            return false;
        }

        var url;
        try {
            url = new URL(href, window.location.href);
        } catch (error) {
            return false;
        }

        if (url.origin !== window.location.origin) {
            return false;
        }

        if (!isMyAccountUrl(url) || isLogoutUrl(url)) {
            return false;
        }

        return true;
    }

    function replaceAccountDom(doc) {
        var freshContent = doc.querySelector('.woocommerce-MyAccount-content');
        var freshNav = doc.querySelector('.woocommerce-MyAccount-navigation');

        if (!freshContent || !freshNav) {
            return false;
        }

        var currentContent = document.querySelector('.woocommerce-MyAccount-content');
        var currentNav = document.querySelector('.woocommerce-MyAccount-navigation');

        if (!currentContent || !currentNav) {
            return false;
        }

        currentContent.replaceWith(freshContent);
        currentNav.replaceWith(freshNav);

        return true;
    }

    function fetchPageHtml(rawUrl) {
        var url = normalizeUrl(rawUrl);
        var cached = cacheGet(url);
        if (cached !== null) {
            return Promise.resolve(cached);
        }

        if (inflight.has(url)) {
            return inflight.get(url);
        }

        var fragmentUrl = buildFragmentUrl(url);

        function doFetch(targetUrl, useFragmentHeader) {
            var headers = {
                'X-Requested-With': 'XMLHttpRequest'
            };
            if (useFragmentHeader) {
                headers[REQUEST_HEADER] = '1';
            }

            return fetch(targetUrl, {
                credentials: 'same-origin',
                headers: headers
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error('Navigation request failed');
                }
                return response.text();
            });
        }

        var request = doFetch(fragmentUrl, true)
            .then(function (html) {
                if (html.indexOf('woocommerce-MyAccount-content') === -1 || html.indexOf('woocommerce-MyAccount-navigation') === -1) {
                    throw new Error('Invalid fragment response');
                }
                return html;
            })
            .catch(function () {
                return doFetch(url, false);
            })
            .then(function (html) {
                cachePut(url, html);
                return html;
            })
            .finally(function () {
                inflight.delete(url);
            });

        inflight.set(url, request);
        return request;
    }

    function swapFromHtml(html) {
        var doc = parser.parseFromString(html, 'text/html');
        if (!replaceAccountDom(doc)) {
            throw new Error('Unable to replace account content');
        }

        var fragmentRoot = doc.querySelector('#mm10-account-fragment');
        var fragmentTitle = fragmentRoot ? fragmentRoot.getAttribute('data-title') : '';

        if (fragmentTitle) {
            document.title = fragmentTitle;
        } else if (doc.title) {
            document.title = doc.title;
        }
    }

    function maybePrefetch(rawUrl) {
        var url = normalizeUrl(rawUrl);
        if (cache.has(url) || inflight.has(url)) {
            return;
        }

        fetchPageHtml(url).catch(function () {
            // Ignore prefetch errors; normal navigation fallback still exists.
        });
    }

    function fetchAndSwap(url, pushState) {
        if (loading) {
            return;
        }

        var normalizedTarget = normalizeUrl(url);
        var normalizedCurrent = normalizeUrl(window.location.href);
        if (normalizedTarget === normalizedCurrent) {
            return;
        }

        loading = true;
        setLoading(true);

        fetchPageHtml(normalizedTarget)
            .then(function (html) {
                swapFromHtml(html);
                if (pushState) {
                    window.history.pushState({ mm10Account: true }, '', normalizedTarget);
                }

                var nav = document.querySelector('.woocommerce-MyAccount-navigation');
                if (nav) {
                    var offset = nav.getBoundingClientRect().top + window.scrollY - 100;
                    window.scrollTo({ top: Math.max(0, offset), behavior: 'auto' });
                }
            })
            .catch(function () {
                window.location.href = normalizedTarget;
            })
            .finally(function () {
                loading = false;
                setLoading(false);
            });
    }

    document.addEventListener('click', function (event) {
        var anchor = event.target.closest('a');
        if (!shouldHandleClick(anchor, event)) {
            return;
        }

        event.preventDefault();
        fetchAndSwap(anchor.href, true);
    });

    document.addEventListener('mouseover', function (event) {
        var anchor = event.target.closest('a');
        if (!anchor || !shouldHandleClick(anchor, event)) {
            return;
        }
        maybePrefetch(anchor.href);
    });

    document.addEventListener('touchstart', function (event) {
        var anchor = event.target.closest('a');
        if (!anchor || !shouldHandleClick(anchor, event)) {
            return;
        }
        maybePrefetch(anchor.href);
    }, { passive: true });

    window.addEventListener('popstate', function () {
        if (!isMyAccountUrl(new URL(window.location.href))) {
            return;
        }

        fetchAndSwap(window.location.href, false);
    });
})();
