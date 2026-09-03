@extends('layouts.portal')
@section('title',config('fuelfree.projects.label','Projects & Our Plans'))
@section('content')
<div class="projects-page">
    <section class="projects-hero">
        <div class="hero-copy">
            <div class="eyebrow-row"><span class="eyebrow">{{ config('fuelfree.projects.eyebrow','Our project portfolio') }}</span><span class="hero-dot"></span><span class="hero-kicker">Operations</span></div>
            <h1>{{ config('fuelfree.projects.label','Projects & Our Plans') }}</h1>
            <p>Manage facilities, technology, capacity and operational status from one clear control panel.</p>
        </div>
        <a class="primary-action" href="{{ route('admin.plants.create') }}"><i class="fa-solid fa-plus"></i><span>Add Project</span></a>
    </section>

    @if(session('success'))
        <div class="notice" role="status"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
    @endif

    <section class="projects-panel" aria-label="Project portfolio">
        <div class="panel-head">
            <div>
                <span class="panel-label">PROJECT PORTFOLIO</span>
                <h2>Projects &amp; Our Plans</h2>
            </div>
            <div class="record-count"><span>{{ $plants->total() }}</span> {{ IlluminateSupportStr::plural('project', $plants->total()) }}</div>
        </div>

        @if($plants->count())
            <div class="project-grid">
                @foreach($plants as $plant)
                    @php
                        $status = strtolower($plant->status ?? 'planned');
                        $statusLabels = ['planned'=>'Planned','operational'=>'Operational','maintenance'=>'Maintenance','offline'=>'Offline'];
                        $statusIcons = ['planned'=>'fa-compass','operational'=>'fa-circle-check','maintenance'=>'fa-screwdriver-wrench','offline'=>'fa-circle-pause'];
                    @endphp
                    <article class="project-card">
                        <div class="card-top">
                            <div class="project-index">{{ str_pad((string)$loop->iteration,2,'0',STR_PAD_LEFT) }}</div>
                            <span class="status-badge status-{{ $status }}"><i class="fa-solid {{ $statusIcons[$status] ?? 'fa-circle-info' }}"></i>{{ $statusLabels[$status] ?? ucfirst($status) }}</span>
                        </div>
                        <div class="project-main">
                            <h3>{{ $plant->name }}</h3>
                            <div class="project-slug">{{ $plant->slug }}</div>
                        </div>
                        <div class="project-meta">
                            <div class="meta-item"><span><i class="fa-solid fa-location-dot"></i> Location</span><strong>{{ $plant->location ?: 'Not specified' }}</strong></div>
                            <div class="meta-item"><span><i class="fa-solid fa-microchip"></i> Technology</span><strong>{{ $plant->technology ?: 'Not specified' }}</strong></div>
                            <div class="meta-item"><span><i class="fa-solid fa-bolt"></i> Capacity</span><strong>{{ $plant->capacity_kw !== null ? number_format($plant->capacity_kw, 3).' kW' : 'Not specified' }}</strong></div>
                        </div>
                        <div class="card-foot">
                            <span class="record-type"><i class="fa-solid fa-layer-group"></i> Project record</span>
                            <a class="edit-action" href="{{ route('admin.plants.edit',$plant) }}"><span>Edit project</span><i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="pagination">{{ $plants->links() }}</div>
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fa-solid fa-industry"></i></div>
                <div><h3>No projects yet</h3><p>No project records have been configured. Add your first project to start building the portfolio.</p></div>
                <a class="secondary-action" href="{{ route('admin.plants.create') }}"><i class="fa-solid fa-plus"></i> Add first project</a>
            </div>
        @endif
    </section>
</div>
@endsection

@push('styles')
<style>
.projects-page{--panel:rgba(6,25,35,.72);--panel-strong:#071d28;--accent:#4dd4f2;--accent-soft:rgba(77,212,242,.1);--line-soft:rgba(104,204,235,.14);max-width:1380px;margin:0 auto}
.projects-hero{display:flex;align-items:flex-end;justify-content:space-between;gap:28px;padding:4px 2px 30px;border-bottom:1px solid var(--line)}
.hero-copy{min-width:0}.eyebrow-row{display:flex;align-items:center;gap:9px;margin-bottom:12px}.hero-kicker{font-size:9px;letter-spacing:.14em;text-transform:uppercase;color:#587b88}.hero-dot{width:3px;height:3px;border-radius:50%;background:#4dd4f2}.eyebrow{color:#58d4ef;font-size:9px;letter-spacing:.18em;font-weight:800;text-transform:uppercase}.projects-hero h1{margin:0;font-size:clamp(30px,4vw,52px);line-height:1.03;letter-spacing:-.035em;font-weight:750;color:#eaf8fb}.projects-hero p{max-width:680px;margin:13px 0 0;color:#8baab6;font-size:13px;line-height:1.65}.primary-action,.secondary-action{display:inline-flex;align-items:center;justify-content:center;gap:9px;text-decoration:none;font-size:11px;font-weight:750;border-radius:11px;transition:.18s ease}.primary-action{flex:0 0 auto;min-height:44px;padding:0 17px;color:#031019;background:linear-gradient(135deg,#69ddf4,#39bfdc);box-shadow:0 10px 28px rgba(35,178,211,.16)}.primary-action:hover{transform:translateY(-1px);box-shadow:0 13px 32px rgba(35,178,211,.24)}.notice{display:flex;align-items:center;gap:10px;margin:20px 0 0;padding:12px 14px;border:1px solid rgba(72,210,157,.18);border-radius:12px;background:rgba(72,210,157,.06);color:#bcebd9;font-size:11px}.notice i{color:#63dbad}.projects-panel{margin-top:24px;border:1px solid var(--line-soft);border-radius:18px;background:linear-gradient(180deg,rgba(7,29,40,.82),rgba(3,18,27,.78));box-shadow:0 22px 70px rgba(0,0,0,.16);overflow:hidden}.panel-head{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:21px 22px;border-bottom:1px solid var(--line-soft);background:rgba(255,255,255,.012)}.panel-label{display:block;margin-bottom:5px;color:#54818e;font-size:8px;letter-spacing:.17em;font-weight:800}.panel-head h2{margin:0;color:#dceff3;font-size:16px;letter-spacing:-.01em}.record-count{padding:7px 10px;border:1px solid var(--line-soft);border-radius:9px;color:#7f9faa;font-size:10px;white-space:nowrap}.record-count span{color:#dff5f8;font-weight:800}.project-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;padding:16px}.project-card{position:relative;min-width:0;border:1px solid rgba(104,204,235,.12);border-radius:15px;background:linear-gradient(180deg,rgba(9,32,43,.9),rgba(5,23,32,.88));padding:16px;transition:transform .18s ease,border-color .18s ease,box-shadow .18s ease}.project-card:hover{transform:translateY(-2px);border-color:rgba(77,212,242,.28);box-shadow:0 14px 34px rgba(0,0,0,.18)}.card-top,.card-foot{display:flex;align-items:center;justify-content:space-between;gap:10px}.project-index{font-size:9px;letter-spacing:.16em;color:#466e7b;font-weight:800}.status-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 8px;border-radius:999px;font-size:8px;font-weight:800;letter-spacing:.04em;text-transform:uppercase;border:1px solid var(--line-soft);color:#a8c4cc;background:rgba(255,255,255,.025)}.status-badge i{font-size:8px}.status-operational{color:#7ee0bd;border-color:rgba(80,216,166,.2);background:rgba(80,216,166,.06)}.status-maintenance{color:#f0cf82;border-color:rgba(240,207,130,.2);background:rgba(240,207,130,.06)}.status-offline{color:#e5a5a5;border-color:rgba(229,165,165,.2);background:rgba(229,165,165,.06)}.status-planned{color:#84d5ea;border-color:rgba(84,213,234,.2);background:rgba(84,213,234,.06)}.project-main{padding:21px 0 16px}.project-main h3{margin:0;color:#e7f5f7;font-size:17px;line-height:1.25;letter-spacing:-.015em;overflow-wrap:anywhere}.project-slug{margin-top:5px;color:#52717d;font-size:9px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.project-meta{display:grid;gap:9px;padding:13px 0;border-top:1px solid rgba(104,204,235,.09);border-bottom:1px solid rgba(104,204,235,.09)}.meta-item{display:flex;align-items:center;justify-content:space-between;gap:12px;min-width:0}.meta-item span{color:#648591;font-size:9px;white-space:nowrap}.meta-item span i{width:13px;color:#4abdd8}.meta-item strong{min-width:0;color:#b9d3d9;font-size:9px;font-weight:600;text-align:right;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.card-foot{padding-top:14px}.record-type{color:#506f7b;font-size:8px;white-space:nowrap}.record-type i{margin-right:5px}.edit-action{display:inline-flex;align-items:center;gap:7px;color:#64d7ef;text-decoration:none;font-size:9px;font-weight:800}.edit-action i{font-size:8px;transition:transform .18s}.edit-action:hover i{transform:translateX(3px)}.empty-state{display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:16px;padding:34px 22px}.empty-icon{width:46px;height:46px;border-radius:13px;display:grid;place-items:center;color:#57cfe9;background:var(--accent-soft);border:1px solid var(--line-soft)}.empty-state h3{margin:0;color:#dceff3;font-size:15px}.empty-state p{margin:5px 0 0;color:#7696a2;font-size:10px;line-height:1.55}.secondary-action{min-height:38px;padding:0 13px;color:#7bdcf0;border:1px solid rgba(77,212,242,.2);background:rgba(77,212,242,.05)}.secondary-action:hover{background:rgba(77,212,242,.1)}.pagination{padding:0 16px 16px}.pagination nav{display:flex;justify-content:center}.pagination svg{width:15px;height:15px}.pagination a,.pagination span{font-size:10px!important}.projects-page a:focus-visible,.projects-page button:focus-visible{outline:2px solid #62d8ef;outline-offset:2px}
@media(max-width:1100px){.project-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:700px){.projects-hero{align-items:flex-start;flex-direction:column;padding-bottom:24px}.projects-hero h1{font-size:34px}.projects-hero p{font-size:12px}.primary-action{width:100%}.panel-head{padding:18px 15px}.project-grid{grid-template-columns:1fr;padding:12px}.project-card{padding:15px}.empty-state{grid-template-columns:auto 1fr;padding:28px 16px}.empty-state .secondary-action{grid-column:1/-1;width:100%}}
@media(max-width:430px){.projects-hero h1{font-size:30px}.panel-head h2{font-size:14px}.record-count{font-size:9px}.meta-item strong{max-width:58%}}
</style>
@endpush
