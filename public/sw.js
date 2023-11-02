let CACHE_NAME = "my-site-cache-v10";
let urlsToCache = [
    // "/",
    "/assets/css/style.css",
    "/assets/css/responsive.css",
    "/css/custom.css",
    "/assets/js/scripts.js",
    "/js/custom.js",
];

self.addEventListener("install", function (event) {
    // Perform install steps
    event.waitUntil(
        caches.open(CACHE_NAME).then(function (cache) {
            return cache.addAll(urlsToCache);
        })
    );
});

self.addEventListener("fetch", function (event) {
    event.respondWith(
        caches.match(event.request).then(function (response) {
            // caches.match() always resolves
            // but in case of success response will have value
            if (response !== undefined) {
                console.log(response);
                return response;
            }
            return fetch(event.request).then(function (response) {
                // response may be used only once
                // we need to save clone to put one copy in cache
                // and serve second one
                let responseClone = response.clone();

                caches.open(CACHE_NAME).then(function (cache) {
                    cache.put(event.request, responseClone);
                });
                return response;
            });
        })
    );
});

self.addEventListener("activate", (event) => {
    let cacheKeeplist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheKeeplist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
