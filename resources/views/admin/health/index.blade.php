@extends('layouts.portal')

@section('title', 'System Health')

@section('content')
<section class="health-admin-head">
    <div class="health-admin-title">
        <span class="eyebrow">PRODUCTION HARDENING · LIVE STATUS</span>
        <h1>System Health</h1>
        <p>Live checks for the application, database, private storage and production safety configuration.</p>
    </div>

    <div class="health-admin-actions">
        <div class="health-count {{ collect($checks)->every(fn($check) => $check['status']) ? 'is-healthy' : 'is-warning' }}">
            <span class="health-count-icon">
                <i class="fa-solid {{ collect($checks)->every(fn($check) => $check['status']) ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
            </span>
            <span>
                <strong>{{ collect($checks)->where('status', true)->count() }}/{{ count($checks) }}</strong>
                <small>checks healthy</small>
            </span>
        </div>
    </div>
</section>

<section class="health-summary {{ collect($checks)->every(fn($check) => $check['status']) ? 'is-healthy' : 'is-warning' }}">
    <div class="health-summary-main">
        <span class="health-summary-icon">
            <i class="fa-solid {{ collect($checks)->every(fn($check) => $check['status']) ? 'fa-shield-halved' : 'fa-shield-exclamation' }}"></i>
        </span>
        <div>
            <span class="eyebrow">SYSTEM STATUS</span>
            <strong>{{ collect($checks)->every(fn($check) => $check['status']) ? 'All checks passed' : 'Attention required' }}</strong>
            <p>{{ collect($checks)->every(fn($check) => $check['status']) ? 'The production environment is currently reporting healthy checks.' : 'One or more production checks require attention.' }}</p>
        </div>
    </div>

    <span class="health-summary-state">
        <i class="fa-solid {{ collect($checks)->every(fn($check) => $check['status']) ? 'fa-check' : 'fa-exclamation' }}"></i>
        {{ collect($checks)->where('status', true)->count() }} of {{ count($checks) }} healthy
    </span>
</section>

<div class="health-grid">
@foreach($checks as $name => $check)
    <article class="health-card {{ $check['status'] ? 'ok' : 'bad' }}">
        <div class="health-card-icon">
            <i class="fa-solid {{ $check['status'] ? 'fa-check' : 'fa-exclamation' }}"></i>
        </div>

        <div class="health-card-content">
            <div class="health-card-top">
                <strong>{{ $name }}</strong>
                <span class="health-badge">
                    <i class="fa-solid {{ $check['status'] ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                    {{ $check['status'] ? 'Healthy' : 'Attention' }}
                </span>
            </div>

            <span class="health-detail">{{ $check['detail'] }}</span>
        </div>
    </article>
@endforeach
</div>
@endsection

@push('styles')
<style>
.health-admin-head{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:28px;
    padding:0 0 20px;
    margin-bottom:18px;
    border-bottom:1px solid rgba(103,208,234,.14);
}

.health-admin-title{min-width:0}

.health-admin-title .eyebrow{
    display:inline-block;
    color:#54cde8;
    font-size:9px;
    font-weight:800;
    letter-spacing:.18em;
}

.health-admin-title h1{
    margin:7px 0;
    color:#eaf8fb;
    font-size:clamp(30px,3.2vw,42px);
    font-weight:800;
    line-height:1.08;
    letter-spacing:-.035em;
}

.health-admin-title p{
    max-width:760px;
    margin:0;
    color:#7898a5;
    font-size:11px;
    line-height:1.7;
}

.health-admin-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    flex:0 0 auto;
}

.health-count{
    display:inline-flex;
    align-items:center;
    gap:9px;
    min-height:44px;
    padding:0 12px;
    border:1px solid var(--line);
    border-radius:10px;
    background:rgba(67,194,229,.025);
}

.health-count-icon{
    width:27px;
    height:27px;
    display:grid;
    place-items:center;
    border-radius:8px;
    font-size:12px;
}

.health-count.is-healthy .health-count-icon{
    background:rgba(67,194,137,.12);
    color:#69dfaa;
}

.health-count.is-warning .health-count-icon{
    background:rgba(235,173,69,.12);
    color:#f1c06b;
}

.health-count strong,
.health-count small{
    display:block;
}

.health-count strong{
    color:#eaf8fb;
    font-size:14px;
    line-height:1.1;
    font-weight:800;
}

.health-count small{
    margin-top:3px;
    color:#7898a5;
    font-size:8px;
    font-weight:700;
    letter-spacing:.04em;
}

.health-summary{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:18px;
    margin-bottom:14px;
    padding:14px 16px;
    border:1px solid var(--line);
    border-radius:14px;
    background:#061923;
}

.health-summary.is-healthy{
    border-color:rgba(67,194,137,.16);
}

.health-summary.is-warning{
    border-color:rgba(235,173,69,.18);
}

.health-summary-main{
    display:flex;
    align-items:center;
    gap:12px;
    min-width:0;
}

.health-summary-icon{
    width:34px;
    height:34px;
    flex:0 0 34px;
    display:grid;
    place-items:center;
    border-radius:9px;
    font-size:13px;
}

.health-summary.is-healthy .health-summary-icon{
    background:rgba(67,194,137,.11);
    color:#69dfaa;
}

.health-summary.is-warning .health-summary-icon{
    background:rgba(235,173,69,.11);
    color:#f1c06b;
}

.health-summary-main .eyebrow{
    display:block;
    margin-bottom:3px;
    color:#54cde8;
    font-size:8px;
    font-weight:800;
    letter-spacing:.16em;
}

.health-summary-main strong{
    display:block;
    color:#eaf8fb;
    font-size:13px;
    font-weight:800;
}

.health-summary-main p{
    margin:3px 0 0;
    color:#7898a5;
    font-size:9px;
    line-height:1.5;
}

.health-summary-state{
    display:inline-flex;
    align-items:center;
    gap:6px;
    flex:0 0 auto;
    color:#78a1ad;
    font-size:9px;
    font-weight:700;
    white-space:nowrap;
}

.health-summary.is-healthy .health-summary-state i{
    color:#69dfaa;
}

.health-summary.is-warning .health-summary-state i{
    color:#f1c06b;
}

.health-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:14px;
}

.health-card{
    display:flex;
    align-items:center;
    gap:13px;
    min-width:0;
    padding:15px;
    border:1px solid var(--line);
    border-radius:14px;
    background:#061923;
    transition:border-color .18s ease,box-shadow .18s ease,transform .18s ease;
}

.health-card:hover{
    transform:translateY(-1px);
    border-color:rgba(78,205,232,.24);
    box-shadow:0 10px 24px rgba(0,0,0,.14);
}

.health-card.ok{
    border-color:rgba(67,194,137,.12);
}

.health-card.bad{
    border-color:rgba(230,76,88,.18);
}

.health-card-icon{
    width:36px;
    height:36px;
    flex:0 0 36px;
    display:grid;
    place-items:center;
    border-radius:10px;
    font-size:13px;
    font-weight:900;
}

.health-card.ok .health-card-icon{
    background:rgba(67,194,137,.12);
    color:#69dfaa;
}

.health-card.bad .health-card-icon{
    background:rgba(230,76,88,.12);
    color:#ff9ba4;
}

.health-card-content{
    min-width:0;
    flex:1;
}

.health-card-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
}

.health-card strong{
    color:#e5f4f7;
    font-size:12px;
    font-weight:800;
    line-height:1.3;
}

.health-badge{
    display:inline-flex;
    align-items:center;
    gap:5px;
    flex:0 0 auto;
    min-height:21px;
    padding:0 7px;
    border:1px solid rgba(255,255,255,.07);
    border-radius:6px;
    background:rgba(255,255,255,.025);
    color:#7898a5;
    font-size:7px;
    font-weight:800;
    letter-spacing:.04em;
    text-transform:uppercase;
}

.health-card.ok .health-badge{
    border-color:rgba(67,194,137,.13);
    background:rgba(67,194,137,.045);
    color:#70cfa5;
}

.health-card.bad .health-badge{
    border-color:rgba(230,76,88,.14);
    background:rgba(230,76,88,.045);
    color:#ff9ba4;
}

.health-detail{
    display:block;
    margin-top:4px;
    color:#7898a5;
    font-size:9px;
    line-height:1.55;
    overflow-wrap:anywhere;
}

@media(max-width:900px){
    .health-admin-head{
        align-items:flex-start;
        flex-direction:column;
        gap:14px;
    }

    .health-admin-actions{
        justify-content:flex-start;
    }

    .health-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:520px){
    .health-admin-head{
        padding-bottom:16px;
        margin-bottom:16px;
    }

    .health-admin-title h1{
        font-size:30px;
    }

    .health-admin-title p{
        font-size:10px;
    }

    .health-summary{
        align-items:flex-start;
        flex-direction:column;
        gap:11px;
        padding:13px;
    }

    .health-summary-state{
        padding-left:46px;
    }

    .health-card{
        padding:13px;
    }

    .health-card-top{
        align-items:flex-start;
        flex-direction:column;
        gap:6px;
    }
}
</style>
@endpush