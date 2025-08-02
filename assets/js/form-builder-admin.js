document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('field-repeater-container');
    const addButton = document.getElementById('add-field-btn');
    if (!container || !addButton) return;

    let fieldIndex = 0;

    const createFieldRow = (index, fieldData = {}) => {
        const row = document.createElement('div');
        row.className = 'field-row';
        // گزینه "آپلود فایل" به این لیست اضافه شده است
        row.innerHTML = `
            <div><label>نام فیلد (Label)</label><input type="text" name="_form_fields[${index}][label]" value="${fieldData.label || ''}" class="widefat" required></div>
            <div>
                <label>نوع فیلد</label>
                <select name="_form_fields[${index}][type]">
                    <option value="text">متن</option>
                    <option value="email">ایمیل</option>
                    <option value="tel">تلفن</option>
                    <option value="textarea">متن بلند</option>
                    <option value="file">آپلود فایل (عکس)</option>
                </select>
            </div>
            <div><label>ضروری؟</label><input type="checkbox" name="_form_fields[${index}][required]" value="true" ${fieldData.required === 'true' ? 'checked' : ''}></div>
            <button type="button" class="button button-link-delete remove-field-btn">حذف</button>
        `;
        const select = row.querySelector('select');
        if (fieldData.type) select.value = fieldData.type;

        row.querySelector('.remove-field-btn').addEventListener('click', () => row.remove());
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
        container.appendChild(createFieldRow(fieldIndex));
        fieldIndex++;
    });

    loadInitialFields();
});