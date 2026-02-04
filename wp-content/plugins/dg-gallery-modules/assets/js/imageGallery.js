(function(){
    var dg_image_gallery = document.querySelectorAll('.dggm_imagegallery');
    [].forEach.call(dg_image_gallery, function(ele, index) {
        var container = ele.querySelector('.dg_ig_container');
        var settings = JSON.parse(container.dataset.settings);
        var image_obj = settings.image_ids.split(",");
		image_obj = image_obj.filter(function (v,i) { return image_obj.indexOf(v) === i});
        var image_count = parseInt(settings.image_count);
        var grid = ele.querySelector('.grid');
        var target = settings.url_target;
        var ig_lightbox_options = {
            ig_lightbox: settings.use_lightbox,
            filter: false,
            filterValue: '',
            download : 'on' === settings.use_lightbox_download ? true : false
        }; 

        if (typeof imagesLoaded === "function") {
            if (typeof Isotope === "function") {
                var iso = new Isotope( grid, {
                    layoutMode: settings.layout_mode,
                    percentPosition: false,
                    itemSelector: '.grid-item',
                    transitionDuration: '0.6s',
                    stagger: 30
                });
                // fix the lazy load layout issue
                var entries = grid.querySelectorAll('.grid-item');
                observer = new IntersectionObserver(function (item) {
                    iso.layout();
                });
                [].forEach.call(entries, function (v){
                    observer.observe(v);
                })
                // *****************
                
                dg_ig_isotop(grid, iso);

                // load more functionality
                if (ele.querySelector('.ig-load-more-btn')) {
                    ele.querySelector('.ig-load-more-btn').addEventListener('click', function(event) {
                        event.preventDefault();
                        ele.querySelector('.ig-load-more-btn').classList.add('loading')
                        
                        var ajaxurl = window.et_pb_custom.ajaxurl;
                        var load_more = container.querySelector('.ig-load-more-btn');
                        var loaded = parseInt(event.target.dataset.loaded);
                        
                        fetch(ajaxurl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'Cache-Control': 'no-cache',
                            },
                            body: new URLSearchParams({
                                et_frontend_nonce: window.et_pb_custom.et_frontend_nonce,
                                action: 'dg_image_gallery_fetch',
                                images: settings.image_ids,
                                page: load_more.dataset.page,
                                loaded: loaded,
                                image_count: parseInt(settings.image_count),
                                options: JSON.stringify(settings)
                            })
                        })
                        .then(function(response) { return response.json()})
                        .then(function(response) {
                            let parser = new DOMParser();
                            let parsedHtml = parser.parseFromString(response.data, 'text/html');
                            var items = parsedHtml.querySelectorAll('.dg_ig_image');
                            if ( loaded >= image_obj.length ) {
                                event.target.style.display = "none";
                            } else {
                                items.forEach(function(item) {
                                    grid.appendChild(item)
                                })
                                loaded = loaded + image_count;
                                event.target.setAttribute("data-loaded", loaded);
                                if(loaded >= image_obj.length){event.target.style.display = "none";}
                            }

                            iso.appended( items )
                            dg_ig_isotop(grid, iso);
                            event.target.classList.remove('loading')
                        })
                        .then(function() {
                            dg_ig_url_open(target, ele);

                            dg_ig_use_lightbox(
                                ele.querySelector('.grid'), 
                                ig_lightbox_options
                            );
                        })
                    })
                }

                // filter
                const filtersElem = ele.querySelector('.dg_filter_buttons');
                
                if (filtersElem !== null) {
                    const buttons = filtersElem.querySelectorAll('.button');

                    filtersElem.addEventListener( 'click', function( event ) {
                        // Ditect mouse click outside button
                        if( !event.target.classList.contains('button')){
                            return;
                        }
                        for ( var i=0, len = buttons.length; i < len; i++ ) {
                            var button = buttons[i];
                            button.classList.remove('is-checked');
                        }
                     
                        // only work with buttons
                        if ( !matchesSelector( event.target, 'button' ) ) {
                          return;
                        }
                        
                        var filterValue = event.target.getAttribute('data-filter');
                        event.target.classList.add('is-checked');
                        
                        // use matching filter function
                        iso.arrange({ filter: filterValue });
                        ig_lightbox_options.filter = true;
                        ig_lightbox_options.filterValue = filterValue;

                        dg_ig_url_open(target, ele);

                        dg_ig_use_lightbox(
                            ele.querySelector('.grid'), 
                            ig_lightbox_options
                        );
                        
                    });
                    const filterValue = filtersElem.querySelector('.is-checked').getAttribute('data-filter');
                    iso.arrange({ filter: filterValue });
                }


                // pagination functionality start
                const pageElem = ele.querySelector('.dg-ig-pagination');
                if (pageElem !== null) {
                    const pageTags = pageElem.querySelectorAll('a');

                    pageElem.addEventListener( 'click', function( event ) {
                        event.preventDefault();
                        window.scrollTo({ top: $(ele).offset().top-150, behavior: 'smooth' });
                        // Ditect mouse click outside button
                        if( !event.target.classList.contains('page-numbers')){
                            return;
                        }
                        let firstPage,lastPage;
                        for ( var i=0, len = pageTags.length; i < len; i++ ) {
                            var pageTag = pageTags[i];
                            pageTag.classList.remove('current');
                        }

                        // only work with buttons
                        if ( !matchesSelector( event.target, 'a' ) ) {
                            return;
                        }

                        let pageNumber = '';
                        const imgCount = event.target.getAttribute('data-count');
                        if('on' === settings.use_number_pagination){
                            if(event.target.classList.contains('prev')){
                                const currentPage = event.target.getAttribute('data-current');
                                if(currentPage === pageTags[1].getAttribute('data-page')){
                                    pageTags[1].classList.add('current');
                                    return;
                                }
                                pageNumber = parseInt(currentPage)-1;
                                pageTags[pageNumber].classList.add('current');
                                pageTags[0].setAttribute('data-current', pageNumber);
                                pageTags[pageTags.length-1].setAttribute('data-current', pageNumber);
                            }
                            else if(event.target.classList.contains('next')){
                                const currentPage = event.target.getAttribute('data-current');
                                if(currentPage === pageTags[(parseInt(pageTags.length)-2)].getAttribute('data-page')){
                                    pageTags[pageTags.length-2].classList.add('current');
                                    return;
                                }
                                pageNumber = parseInt(currentPage)+1;
                                pageTags[pageNumber].classList.add('current');
                                pageTags[0].setAttribute('data-current', pageNumber);
                                pageTags[pageTags.length-1].setAttribute('data-current', pageNumber);
                            }
                            else{
                                pageNumber = event.target.getAttribute('data-page');
                                event.target.classList.add('current');
                                pageTags[0].setAttribute('data-current', pageNumber);
                                pageTags[pageTags.length-1].setAttribute('data-current', pageNumber);
                            }
                            (parseInt(pageNumber) > 1)?pageTags[0].style.display = 'block':pageTags[0].style.display = 'none';
                            (parseInt(pageNumber) === pageTags.length-2)?pageTags[pageTags.length-1].style.display = 'none':pageTags[pageTags.length-1].style.display = 'block';
                        }
                        else{
                            if(event.target.classList.contains('prev')){
                                const currentPage = event.target.getAttribute('data-current');
                                pageNumber = parseInt(currentPage)-1;
                                pageTags[0].setAttribute('data-current', pageNumber);
                                pageTags[pageTags.length-1].setAttribute('data-current', pageNumber);
                            }
                            else if(event.target.classList.contains('next')){
                                const currentPage = event.target.getAttribute('data-current');
                                pageNumber = parseInt(currentPage)+1;
                                pageTags[0].setAttribute('data-current', pageNumber);
                                pageTags[pageTags.length-1].setAttribute('data-current', pageNumber);
                            }
                            (parseInt(pageNumber) > 1)?pageTags[0].style.display = 'block':pageTags[0].style.display = 'none';
                            (parseInt(pageNumber) === Math.ceil(settings.image_ids.split(",").length/parseInt(imgCount)))?pageTags[pageTags.length-1].style.display = 'none':pageTags[pageTags.length-1].style.display = 'block';

                        }

                        var ajaxurl = window.et_pb_custom.ajaxurl;
                        fetch(ajaxurl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'Cache-Control': 'no-cache',
                            },
                            body: new URLSearchParams({
                                et_frontend_nonce: window.et_pb_custom.et_frontend_nonce,
                                action: 'dg_image_gallery_fetch_page_data',
                                images: settings.image_ids,
                                page: parseInt(pageNumber),
                                image_count: parseInt(imgCount),
                                options: JSON.stringify(settings)
                            })
                        })
                            .then(function(response) { return response.json()})
                            .then(function(response) {
                                let parser = new DOMParser();
                                let parsedHtml = parser.parseFromString(response.data, 'text/html');
                                var items = parsedHtml.querySelectorAll('.dg_ig_image');
                                grid.innerHTML = ''
                                if ( parseInt(imgCount) >= image_obj.length) {
                                    event.target.style.display = "none";
                                } else {
                                    items.forEach(function(item) {
                                        grid.appendChild(item);
                                    })
                                    if(parseInt(imgCount) >= image_obj.length){event.target.style.display = "none";}
                                }
                                iso = new Isotope( grid, {
                                    layoutMode: settings.layout_mode,
                                    percentPosition: true,
                                    itemSelector: '.grid-item',
                                    transitionDuration: '0.6s',
                                    stagger: 30
                                });
                                dg_ig_isotop(grid, iso);
                            })
                            .then(function() {
                                dg_ig_url_open(target, ele);

                                dg_ig_use_lightbox(
                                    ele.querySelector('.grid'),
                                    ig_lightbox_options
                                );
                            })
                    });
                }
                // pagination functionality end

                dg_ig_url_open(target, ele);

                dg_ig_use_lightbox(
                    ele.querySelector('.grid'), 
                    ig_lightbox_options
                );

                // const filterValue = filtersElem.querySelector('.is-checked').getAttribute('data-filter');
                // iso.arrange({ filter: filterValue });
                
            }


            
        }

    })
})()

function dg_ig_isotop(selector, iso) {
    imagesLoaded(selector).on('progress', function() {
        iso.layout()
    }).on('done', function() {
        selector.style.opacity = 1;
    })
    
}

function dg_ig_use_lightbox(selector, options) {
    if (options.ig_lightbox === 'on') {
        var settings = {
            subHtmlSelectorRelative: true,
            addClass: 'dg_ig_lightbox',
            counter: false,
            download: options.download
        };
        
        if (options.filter) {
            settings.selector= options.filterValue.replace('*', '');
            // window.lgData[selector.getAttribute('lg-uid')].destroy(true);
        }
        
        lightGallery(selector,settings);
    }
}

function dg_ig_url_open(target, ele) {   
    var elements = ele.querySelectorAll('.item-content');
    [].forEach.call(elements, function(image, index) {
        var url = image.dataset.customurl;
        if(url && url !== '') {
            image.addEventListener('click', function(event) {
                if (target === 'same_window') {
                    window.location = url;
                } else {
                    window.open(url)
                }
            })
        }
    })
}

function igHandleClick(event) {
    return false;
}

