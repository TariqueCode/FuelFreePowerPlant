<style>
/* Company dropdown must escape the horizontal navigation clipping on desktop. */
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

            function setOpen(open){
                dropdown.classList.toggle('is-open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            }

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
