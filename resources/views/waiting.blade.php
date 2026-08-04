<div id="loading-wrapper" class="loading-wrapper" role="status" aria-live="polite">
    <div class="loading-card text-center">
        <div class="loading-mark" aria-hidden="true">
            <div class="loading-spinner"></div>
            <i class="fa-solid fa-heart-pulse"></i>
        </div>
        <h2 class="h6 fw-bold mb-1">กำลังประมวลผล</h2>
        <p class="small text-muted mb-0">กรุณารอสักครู่</p>
    </div>
</div>

<style>
    .loading-wrapper {
        position: fixed;
        inset: 0;
        z-index: 4000;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 54, 29, .34);
        backdrop-filter: blur(4px);
    }

    .loading-card {
        width: min(88vw, 320px);
        padding: 2.4rem;
        border: 1px solid rgba(255, 255, 255, .7);
        border-radius: 20px;
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 20px 50px rgba(0, 35, 19, .25);
        animation: loading-card-enter .2s ease-out;
    }

    .loading-mark {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 72px;
        height: 72px;
        margin-bottom: 1.25rem;
        color: #006637;
        font-size: 1.45rem;
    }

    .loading-mark::before {
        position: absolute;
        inset: 12px;
        border-radius: 50%;
        background: #e8f5ed;
        content: '';
    }

    .loading-mark i { position: relative; }

    .loading-spinner {
        position: absolute;
        inset: 0;
        border: 4px solid #d8ebe0;
        border-top-color: #006637;
        border-radius: 50%;
        animation: loading-spin .7s linear infinite;
    }

    @keyframes loading-spin { to { transform: rotate(360deg); } }
    @keyframes loading-card-enter { from { opacity: 0; transform: translateY(8px) scale(.98); } to { opacity: 1; transform: none; } }
</style>
