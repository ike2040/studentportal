document.addEventListener('DOMContentLoaded', () => {
    const stateSelect = document.getElementById('state_of_origin');
    const lgaSelect = document.getElementById('lga');
    const imageInput = document.getElementById('profile_image');
    const imagePreview = document.getElementById('preview-img');
    
    let statesData = [];

    // 1. Fetch Nigerian States and LGAs JSON
    fetch('states-localgovts.json')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            statesData = data.states;
            populateStates();
        })
        .catch(error => {
            console.error('Error loading states and LGAs:', error);
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'Failed to load states';
            stateSelect.appendChild(opt);
        });

    // 2. Populate States Dropdown
    function populateStates() {
        stateSelect.innerHTML = '<option value="" disabled selected>Select State</option>';
        
        statesData.forEach(stateObj => {
            const opt = document.createElement('option');
            opt.value = stateObj.state;
            opt.textContent = stateObj.state;
            stateSelect.appendChild(opt);
        });
    }

    // 3. Handle State Selection Change (Populate LGAs)
    stateSelect.addEventListener('change', (e) => {
        const selectedStateName = e.target.value;
        lgaSelect.innerHTML = '<option value="" disabled selected>Select Local Government</option>';
        lgaSelect.disabled = true;

        const foundState = statesData.find(s => s.state === selectedStateName);
        if (foundState && foundState.local) {
            foundState.local.forEach(lga => {
                const opt = document.createElement('option');
                opt.value = lga;
                opt.textContent = lga;
                lgaSelect.appendChild(opt);
            });
            lgaSelect.disabled = false;
        }
    });

    // 4. Handle Image Upload Client-Side Preview
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file (PNG, JPG, JPEG, WEBP).');
                    imageInput.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                // Check file size (limit to 5MB)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File size exceeds the 5MB limit.');
                    imageInput.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (event) => {
                    imagePreview.src = event.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
    }
});
