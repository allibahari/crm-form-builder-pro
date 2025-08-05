document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('field-repeater-container');
    const addButton = document.getElementById('add-field-btn');
    if (!container || !addButton) return;

    let fieldIndex = 0;

    const createFieldRow = (index, fieldData = {}) => {
        const row = document.createElement('div');
        row.className = 'field-row';

        const fieldType = fieldData.type || 'text';
        const fieldLabel = fieldData.label || 'فیلد جدید';

        row.innerHTML = `
            <div class="field-header">
                <span class="dashicons dashicons-move"></span>
                <strong class="field-label-preview">${fieldLabel}</strong>
                <span class="field-type-preview">${fieldType}</span>
                <a href="#" class="toggle-details" title="باز/بسته کردن جزئیات"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
                <a href="#" class="remove-field-btn button-link-delete" title="حذف فیلد"><span class="dashicons dashicons-trash"></span></a>
            </div>
            <div class="field-details hidden">
                <div class="form-field">
                    <label>نام فیلد (Label)</label>
                    <input type="text" class="field-label-input" name="_form_fields[${index}][label]" value="${fieldData.label || ''}" required>
                </div>
                <div class="form-field">
                    <label>نوع فیلد</label>
                    <select class="field-type-select" name="_form_fields[${index}][type]">
                        <option value="text">متن</option>
                        <option value="email">ایمیل</option>
                        <option value="tel">تلفن</option>
                        <option value="textarea">متن بلند</option>
                        <option value="file">آپلود فایل (عکس)</option>
                    </select>
                </div>
                <div class="form-field">
                    <label>متن جایگزین (Placeholder)</label>
                    <input type="text" name="_form_fields[${index}][placeholder]" value="${fieldData.placeholder || ''}">
                </div>
                <div class="form-field">
                    <label>
                        <input type="checkbox" name="_form_fields[${index}][required]" value="true" ${fieldData.required === 'true' ? 'checked' : ''}>
                        ضروری است؟
                    </label>
                </div>
                <div class="form-field">
                    <label>حداقل طول کاراکتر</label>
                    <input type="number" name="_form_fields[${index}][minlength]" value="${fieldData.minlength || ''}" min="0">
                </div>
                <div class="form-field">
                    <label>حداکثر طول کاراکتر</label>
                    <input type="number" name="_form_fields[${index}][maxlength]" value="${fieldData.maxlength || ''}" min="0">
                </div>
            </div>
        `;

        // Set selected type
        const select = row.querySelector('.field-type-select');
        select.value = fieldType;

        // Event Listeners
        row.querySelector('.toggle-details').addEventListener('click', e => {
            e.preventDefault();
            row.querySelector('.field-details').classList.toggle('hidden');
            const icon = e.currentTarget.querySelector('.dashicons');
            icon.classList.toggle('dashicons-arrow-down-alt2');
            icon.classList.toggle('dashicons-arrow-up-alt2');
        });

        row.querySelector('.remove-field-btn').addEventListener('click', e => {
            e.preventDefault();
            if (confirm('آیا از حذف این فیلد مطمئن هستید؟')) {
                row.remove();
            }
        });

        row.querySelector('.field-label-input').addEventListener('input', e => {
            row.querySelector('.field-label-preview').textContent = e.target.value || 'فیلد جدید';
        });
        
        row.querySelector('.field-type-select').addEventListener('change', e => {
            row.querySelector('.field-type-preview').textContent = e.target.value;
        });

        return row;
    };

    const loadInitialFields = () => {
        if (window.crm_form_fields && window.crm_form_fields.length > 0) {
            window.crm_form_fields.forEach((field, index) => {
                container.appendChild(createFieldRow(index, field));
            });
            fieldIndex = window.crm_form_fields.length;
        }
    };

    addButton.addEventListener('click', () => {
        const newRow = createFieldRow(fieldIndex);
        container.appendChild(newRow);
        // Open details for the new field by default
        newRow.querySelector('.toggle-details').click();
        fieldIndex++;
    });

    loadInitialFields();
});