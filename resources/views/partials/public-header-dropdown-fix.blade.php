<style>
/* Keep the Company dropdown visible on desktop and remove duplicate items. */
@media (min-width:721px){
    .public-header-nav,
    .public-menu{overflow:visible!important;}
}
</style>
<script>
(function(){
    function bindCompanyDropdown(){
        document.querySelectorAll('[data-company-menu]').forEach(function(dropdown){
            if(dropdown.dataset.dropdownFixBound === '1') return;
            var toggle = dropdown.querySelector('.public-menu-dropdown-toggle');
            if(!toggle) return;
            dropdown.dataset.dropdownFixBound = '1';

            function removeDuplicateLinks(){
                var panel = dropdown.querySelector('.public-menu-dropdown-panel');
                if(!panel) return;
                var seenHref = Object.create(null);
                var seenLabel = Object.create(null);
                panel.querySelectorAll('a').forEach(function(link){
                    var href = (link.getAttribute('href') || '').replace(/\/$/, '');
                    var label = (link.textContent || '').trim().replace(/\s+/g, ' ').toLowerCase();
                    if((href && seenHref[href]) || (label && seenLabel[label])){
                        link.remove();
                        return;
                    }
                    if(href) seenHref[href] = true;
                    if(label) seenLabel[label] = true;
                });
            }

            function setOpen(open){
                removeDuplicateLinks();
                dropdown.classList.toggle('is-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            }

            removeDuplicateLinks();

            toggle.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                setOpen(!dropdown.classList.contains('is-open'));
            }, true);

            document.addEventListener('click', function(e){
                if(!dropdown.contains(e.target)) setOpen(false);
            });

            document.addEventListener('keydown', function(e){
                if(e.key === 'Escape') setOpen(false);
            });
        });
    }

    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', bindCompanyDropdown);
    }else{
        bindCompanyDropdown();
    }
})();
</script>
