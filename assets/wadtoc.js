(function() {
    function generateTOC(options) {
        var headings = Array.isArray(options.headings) ? options.headings : ['h1','h2','h3','h4','h5','h6'];
    
        // Construire le sélecteur CSS avec les niveaux demandés
        var selector = headings.map(h => 'article ' + h).join(',');
    
        // Sélectionner tous les titres dans l'ordre naturel du DOM
        var nodes = Array.from(document.querySelectorAll(selector))
            .filter(el => !el.closest('.wadtoc-wrap') && el.offsetParent !== null);
    
        if (!nodes.length) return '';
    
        var ul = document.createElement('ul');
        ul.className = 'wadtoc-list';
    
        nodes.forEach(function(el, i) {
            var tag = el.tagName.toLowerCase();
            var title = el.textContent.trim();
            var anchor = 'wadtoc-' + title.toLowerCase()
                .replace(/[^a-z0-9]+/g,'-')
                .replace(/^-+|-+$/g,'') + '-' + (i+1);
            if (!el.id) el.id = anchor;
    
            var li = document.createElement('li');
            li.className = 'wadtoc-item wadtoc-' + tag;
    
            var a = document.createElement('a');
            a.href = '#' + anchor;
            a.textContent = title;
            a.style.color = options.linkcolor || '#444';
            a.style.textDecoration = 'none';
    
            // Calcul du niveau (ex: h2 => 2)
            const level = parseInt(tag.replace('h', ''), 10);
            a.classList.add('wadtoc-level-' + level);
    
            li.appendChild(a);
            ul.appendChild(li);
        });
    
        return ul.outerHTML;
    }
    
    function renderTOC(wrap, options) {
        wrap.innerHTML = '';
        wrap.classList.remove('active');
        if (options.open === '1' || options.open === true) {
            wrap.classList.add('active');
        }

        wrap.style.background = options.bgcolor || '#fefefe';
        if (options.maxwidth) {
            wrap.style.maxWidth = parseInt(options.maxwidth, 10) + 'px';
        } else {
            wrap.style.removeProperty('max-width');
        }

        var header = document.createElement('div');
        header.className = 'wadtoc-header';
        header.style.color = options.headercolor || '#444';

        var title = document.createElement('span');
        title.className = 'wadtoc-title';
        title.textContent = options.title || 'Sommaire';

        var btn = document.createElement('button');
        btn.className = 'wadtoc-toggle';
        btn.setAttribute('aria-label','Afficher/masquer le sommaire');

        var icon = document.createElement('span');
        icon.className = 'wadtoc-toggle-icon';

        var iconColor = options.iconcolor || '#444';
        icon.style.setProperty('--wadtoc-icon-color', iconColor);

        btn.appendChild(icon);
        header.appendChild(title);
        header.appendChild(btn);
        wrap.appendChild(header);

        var nav = document.createElement('nav');
        nav.className = 'wadtoc-content';
        nav.innerHTML = generateTOC(options);
        wrap.appendChild(nav);

        function toggle() {
            wrap.classList.toggle('active');
        }

        btn.addEventListener('click', function(e){
            e.preventDefault();
            toggle();
        });

        header.addEventListener('click', function(e){
            if (e.target === btn || btn.contains(e.target)) return;
            toggle();
        });
    }

    function parseOptions(wrap) {
        try {
            var data = wrap.getAttribute('data-wadtoc');
            if (!data) return {};
            var options = JSON.parse(data);
            if (options.headings && typeof options.headings === 'object' && !Array.isArray(options.headings)) {
                options.headings = Object.values(options.headings);
            }
            return options;
        } catch(e) {
            console.warn('Erreur parsing options TOC:', e);
            return {};
        }
    }

    var debounceTimer = null;
    function initAllTOC() {
        if (debounceTimer) clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            document.querySelectorAll('.wadtoc-wrap').forEach(function(wrap){
                var options = parseOptions(wrap);
                if (!wrap.hasAttribute('data-wadtoc-rendered')) {
                    renderTOC(wrap, options);
                    wrap.setAttribute('data-wadtoc-rendered','1');
                }
            });
        }, 100);
    }

    var ready = false;
    function readyInit() {
        if (ready) return;
        ready = true;
        setTimeout(initAllTOC, 50);
    }

    document.addEventListener('DOMContentLoaded', readyInit);
    window.addEventListener('load', readyInit);
    var observer = new MutationObserver(function(){initAllTOC();});
    observer.observe(document.body, {childList:true,subtree:true});
})();
