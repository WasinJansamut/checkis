// Shared SweetAlert2 helpers for application views.
const appSwalConfirmThemes = {
    save: { icon: 'fa-floppy-disk', label: 'ยืนยันการบันทึก', tone: 'save' },
    delete: { icon: 'fa-trash-can', label: 'ยืนยันการลบ', tone: 'delete' },
    action: { icon: 'fa-circle-check', label: 'ยืนยันการทำรายการ', tone: 'action' },
    logout: { icon: 'fa-arrow-right-from-bracket', label: 'ยืนยันการออกจากระบบ', tone: 'action' }
};

const appSwalEscapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (character) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
})[character]);

const appSwalConfirmDialog = (type, { title, text, confirmButtonText }) => {
    const theme = appSwalConfirmThemes[type];

    return Swal.fire({
        html: `
            <div class="app-swal-confirm__content">
                <div class="app-swal-confirm__icon app-swal-confirm__icon--${theme.tone}">
                    <i class="fa-solid ${theme.icon}"></i>
                </div>
                <div class="app-swal-confirm__eyebrow">${theme.label}</div>
                <h2 class="app-swal-confirm__title">${appSwalEscapeHtml(title)}</h2>
                ${text ? `<p class="app-swal-confirm__text">${appSwalEscapeHtml(text)}</p>` : ''}
            </div>`,
        showCancelButton: true,
        confirmButtonText: `<i class="fa-solid ${theme.icon} me-1"></i> ${appSwalEscapeHtml(confirmButtonText)}`,
        cancelButtonText: '<i class="fa-solid fa-xmark me-1"></i> ยกเลิก',
        buttonsStyling: false,
        focusCancel: true,
        reverseButtons: true,
        width: '430px',
        customClass: {
            popup: 'app-swal-confirm',
            htmlContainer: 'app-swal-confirm__html',
            actions: 'app-swal-confirm__actions',
            confirmButton: `app-swal-confirm__button app-swal-confirm__button--${theme.tone}`,
            cancelButton: 'app-swal-confirm__button app-swal-confirm__button--cancel'
        }
    });
};

const appSwalFeedbackDialog = (type, title, text) => {
    const isSuccess = type === 'success';
    const icon = isSuccess ? 'fa-circle-check' : 'fa-triangle-exclamation';
    const eyebrow = isSuccess ? 'ดำเนินการเรียบร้อย' : 'ไม่สามารถดำเนินการได้';

    return Swal.fire({
        html: `
            <div class="app-swal-feedback__content">
                <div class="app-swal-feedback__icon app-swal-feedback__icon--${type}">
                    <i class="fa-solid ${icon}"></i>
                </div>
                <div class="app-swal-feedback__eyebrow">${eyebrow}</div>
                <h2 class="app-swal-feedback__title">${appSwalEscapeHtml(title)}</h2>
                ${text ? `<p class="app-swal-feedback__text">${appSwalEscapeHtml(text)}</p>` : ''}
            </div>`,
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> ตกลง',
        buttonsStyling: false,
        width: '410px',
        customClass: {
            popup: 'app-swal-feedback',
            htmlContainer: 'app-swal-feedback__html',
            actions: 'app-swal-feedback__actions',
            confirmButton: `app-swal-feedback__button app-swal-feedback__button--${type}`
        }
    });
};

window.AppSwal = {
    success(title, text = '') {
        return appSwalFeedbackDialog('success', title, text);
    },

    error(title = 'เกิดข้อผิดพลาด', text = '') {
        return appSwalFeedbackDialog('error', title, text);
    },

    confirm(title, text, confirmButtonText = 'ยืนยัน') {
        return appSwalConfirmDialog('action', { title, text, confirmButtonText });
    },

    confirmSave({ title = 'บันทึกข้อมูล?', text = 'ต้องการบันทึกข้อมูลใช่หรือไม่?', confirmButtonText = 'บันทึก' } = {}) {
        return appSwalConfirmDialog('save', { title, text, confirmButtonText });
    },

    confirmDelete({ title = 'ลบรายการ?', text = 'รายการนี้จะไม่สามารถกู้คืนได้', confirmButtonText = 'ลบรายการ' } = {}) {
        return appSwalConfirmDialog('delete', { title, text, confirmButtonText });
    },

    confirmAction({ title = 'ยืนยันการทำรายการ?', text = '', confirmButtonText = 'ยืนยัน' } = {}) {
        return appSwalConfirmDialog('action', { title, text, confirmButtonText });
    },

    confirmLogout() {
        return appSwalConfirmDialog('logout', {
            title: 'ออกจากระบบ',
            text: 'ต้องการออกจากระบบใช่หรือไม่?',
            confirmButtonText: 'ออกจากระบบ'
        });
    },

    lineQr() {
        return Swal.fire({
            html: `
                <div class="app-line-qr__hero">
                    <span class="app-line-qr__icon"><i class="fa-brands fa-line"></i></span>
                    <div class="app-line-qr__eyebrow">IS - CHECKING SUPPORT</div>
                    <h2 class="app-line-qr__title">ติดต่อทีมสนับสนุน</h2>
                    <p class="app-line-qr__subtitle">สแกน QR Code เพื่อพูดคุยกับเจ้าหน้าที่ผ่าน LINE</p>
                </div>
                <div class="app-line-qr__body">
                    <div class="app-line-qr__frame">
                        <img src="https://rti.moph.go.th/pher-plus/report/public/assets/images/qrcode_line.png" alt="QR Code Line @rtiddc">
                    </div>
                    <div class="app-line-qr__account"><i class="fa-brands fa-line"></i><span>LINE Official Account</span><strong>@rtiddc</strong></div>
                    <a class="app-line-qr__link" href="https://lin.ee/qzzSV3f" target="_blank" rel="noopener"><i class="fa-brands fa-line me-1"></i> เปิด LINE เพื่อพูดคุย</a>
                </div>
                `,
            confirmButtonText: '<i class="fa-solid fa-xmark me-1"></i> ปิดหน้าต่าง',
            showCloseButton: true,
            buttonsStyling: false,
            width: '400px',
            customClass: {
                popup: 'app-line-qr',
                htmlContainer: 'app-line-qr__html',
                actions: 'app-line-qr__actions',
                confirmButton: 'app-line-qr__close'
            }
        });
    }
};

document.addEventListener('click', (event) => {
    if (event.target.closest('#line_qr_code')) {
        window.AppSwal.lineQr();
    }
});
