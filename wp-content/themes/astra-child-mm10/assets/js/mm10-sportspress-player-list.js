(function () {
    var listRoot = document.querySelector('.sp-template-player-list');
    if (!listRoot) {
        return;
    }

    var config = window.mm10SportsPressList || {};
    var apiBase = (config.apiBase || '/wp-json/wp/v2').replace(/\/$/, '');
    var mediaEmbedKey = 'wp:featuredmedia';

    var memoryCache = new Map();

    function getSlugFromUrl(href) {
        if (!href) {
            return '';
        }

        try {
            var url = new URL(href, window.location.origin);
            var path = url.pathname.replace(/\/$/, '');
            var segments = path.split('/').filter(Boolean);
            return segments.length ? segments[segments.length - 1] : '';
        } catch (error) {
            return '';
        }
    }

    function getCachedPhoto(slug) {
        if (!slug) {
            return null;
        }

        if (memoryCache.has(slug)) {
            return memoryCache.get(slug);
        }

        try {
            var value = window.sessionStorage.getItem('mm10_sp_player_photo_' + slug);
            if (value) {
                memoryCache.set(slug, value);
                return value;
            }
        } catch (error) {
            // Ignore storage failures.
        }

        return null;
    }

    function setCachedPhoto(slug, url) {
        if (!slug || !url) {
            return;
        }

        memoryCache.set(slug, url);

        try {
            window.sessionStorage.setItem('mm10_sp_player_photo_' + slug, url);
        } catch (error) {
            // Ignore storage failures.
        }
    }

    function fetchPlayerPhoto(slug) {
        var cached = getCachedPhoto(slug);
        if (cached) {
            return Promise.resolve(cached);
        }

        if (!slug) {
            return Promise.resolve('');
        }

        var endpoint = apiBase + '/sp_player?slug=' + encodeURIComponent(slug) + '&_embed=' + encodeURIComponent(mediaEmbedKey);
        return fetch(endpoint, { credentials: 'same-origin' })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Photo fetch failed');
                }
                return response.json();
            })
            .then(function (items) {
                if (!Array.isArray(items) || !items.length) {
                    return '';
                }

                var item = items[0] || {};
                var embed = item._embedded && item._embedded[mediaEmbedKey] ? item._embedded[mediaEmbedKey][0] : null;
                if (!embed) {
                    return '';
                }

                var sizes = embed.media_details && embed.media_details.sizes ? embed.media_details.sizes : {};
                var thumb = sizes.thumbnail && sizes.thumbnail.source_url ? sizes.thumbnail.source_url : '';
                var medium = sizes.medium && sizes.medium.source_url ? sizes.medium.source_url : '';
                var source = thumb || medium || embed.source_url || '';

                if (source) {
                    setCachedPhoto(slug, source);
                }

                return source;
            })
            .catch(function () {
                return '';
            });
    }

    function createAvatar(photoUrl, playerName) {
        var avatar = document.createElement('span');
        avatar.className = 'mm10-sp-player-avatar';

        if (photoUrl) {
            var img = document.createElement('img');
            img.src = photoUrl;
            img.alt = playerName ? playerName + ' profile photo' : 'Player profile photo';
            img.loading = 'lazy';
            avatar.appendChild(img);
        } else {
            avatar.classList.add('is-placeholder');
            var initial = (playerName || '?').trim().charAt(0).toUpperCase() || '?';
            avatar.textContent = initial;
        }

        return avatar;
    }

    function enhancePlayerCell(cell, playerLink, photoUrl) {
        if (!cell || !playerLink || cell.classList.contains('mm10-sp-player-cell-ready')) {
            return;
        }

        var wrap = document.createElement('div');
        wrap.className = 'mm10-sp-player-cell';

        var name = playerLink.textContent ? playerLink.textContent.trim() : '';
        var avatar = createAvatar(photoUrl, name);

        var clonedLink = playerLink.cloneNode(true);
        clonedLink.classList.add('mm10-sp-player-link');

        wrap.appendChild(avatar);
        wrap.appendChild(clonedLink);

        cell.textContent = '';
        cell.appendChild(wrap);
        cell.classList.add('mm10-sp-player-cell-ready');
    }

    var rows = listRoot.querySelectorAll('.sp-data-table tbody tr');
    rows.forEach(function (row) {
        var playerLink = row.querySelector('a[href]');
        if (!playerLink) {
            return;
        }

        var cell = playerLink.closest('td');
        if (!cell) {
            return;
        }

        var slug = getSlugFromUrl(playerLink.getAttribute('href'));
        fetchPlayerPhoto(slug).then(function (photoUrl) {
            enhancePlayerCell(cell, playerLink, photoUrl);
        });
    });
})();
