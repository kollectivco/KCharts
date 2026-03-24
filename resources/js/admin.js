document.addEventListener('DOMContentLoaded', () => {

    // Theme logic
    const body = document.body;
    const themeToggle = document.getElementById('kc-theme-toggle');
    const storedTheme = localStorage.getItem('kc-theme');

    if (storedTheme === 'dark') {
        body.classList.add('kc-theme-dark');
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            if (body.classList.contains('kc-theme-dark')) {
                body.classList.remove('kc-theme-dark');
                localStorage.setItem('kc-theme', 'light');
            } else {
                body.classList.add('kc-theme-dark');
                localStorage.setItem('kc-theme', 'dark');
            }
        });
    }

    // Upload Handler dummy for now
    const uploadForm = document.getElementById('kc-upload-form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const btn = uploadForm.querySelector('button[type="submit"]');
            btn.textContent = "Processing...";
            btn.disabled = true;

            const formData = new FormData(uploadForm);
            formData.append('action', 'kc_upload_chart_data');
            formData.append('nonce', kcObject.nonce);
            
            fetch(kcObject.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.textContent = "Import Complete";
                if(data.success) {
                    const stats = data.data;
                    alert(`Import successful!\n\nImported Rows: ${stats.rows}\nNew Artists: ${stats.artists_created}\nNew Tracks: ${stats.tracks_created}\nChart Rebuilt Automatically.\n\nClick OK to refresh dashboard.`);
                    window.location.reload();
                } else {
                    alert("Error: " + data.data.message);
                    btn.textContent = "Import Data";
                    btn.disabled = false;
                }
            })
            .catch(err => {
                alert("An error occurred");
                console.error(err);
                btn.textContent = "Import Data";
                btn.disabled = false;
            });
        });
    }

    // Publish Chart Week JS Fix
    document.querySelectorAll('.kc-publish-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = e.target.getAttribute('data-id');
            if ( ! confirm("Publish this chart? It will appear immediately on the frontend public shortcodes.") ) return;

            e.target.textContent = 'Publishing...';
            e.target.disabled = true;

            const formData = new FormData();
            formData.append('action', 'kc_publish_week');
            formData.append('nonce', kcObject.nonce);
            formData.append('week_id', id);

            fetch(kcObject.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert("Error: " + data.data.message);
                    e.target.textContent = 'Publish';
                    e.target.disabled = false;
                }
            })
            .catch(err => {
                alert("An error occurred.");
                console.error(err);
                e.target.textContent = 'Publish';
                e.target.disabled = false;
            });
        });
    });

    // Unpublish Chart Week JS
    document.querySelectorAll('.kc-unpublish-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = e.target.getAttribute('data-id');
            if ( ! confirm("Unpublish this chart? It will immediately disappear from the frontend.") ) return;

            e.target.textContent = 'Unpublishing...';
            e.target.disabled = true;

            const formData = new FormData();
            formData.append('action', 'kc_unpublish_week');
            formData.append('nonce', kcObject.nonce);
            formData.append('week_id', id);

            fetch(kcObject.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert("Error: " + data.data.message);
                    e.target.textContent = 'Unpublish';
                    e.target.disabled = false;
                }
            })
            .catch(err => {
                alert("An error occurred.");
                console.error(err);
                e.target.textContent = 'Unpublish';
                e.target.disabled = false;
            });
        });
    });

    // Rebuild Chart Week JS
    document.querySelectorAll('.kc-rebuild-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = e.target.getAttribute('data-id');
            if ( ! confirm("Rebuild this chart? This will recalculate the chart based on the latest uploaded metrics for this week.") ) return;

            e.target.textContent = 'Rebuilding...';
            e.target.disabled = true;

            const formData = new FormData();
            formData.append('action', 'kc_rebuild_week');
            formData.append('nonce', kcObject.nonce);
            formData.append('week_id', id);

            fetch(kcObject.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert("Chart rebuilt successfully!");
                    window.location.reload();
                } else {
                    alert("Error: " + data.data.message);
                    e.target.textContent = 'Rebuild';
                    e.target.disabled = false;
                }
            })
            .catch(err => {
                alert("An error occurred.");
                console.error(err);
                e.target.textContent = 'Rebuild';
                e.target.disabled = false;
            });
        });
    });

    // Review Resolution JS
    document.querySelectorAll('.kc-resolve-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = e.target.getAttribute('data-id');
            const row = e.target.closest('tr');
            const artistId = row.querySelector('.kc-resolve-artist').value;
            const trackTitle = row.querySelector('.kc-resolve-track-title').value;

            if ( ! artistId || ! trackTitle ) {
                alert("Please select an artist and ensure track title is provided.");
                return;
            }

            e.target.textContent = 'Saving...';
            e.target.disabled = true;

            const formData = new FormData();
            formData.append('action', 'kc_resolve_row');
            formData.append('nonce', kcObject.nonce);
            formData.append('row_id', id);
            formData.append('artist_id', artistId);
            formData.append('track_title', trackTitle);

            fetch(kcObject.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    row.style.background = '#dcfce7';
                    setTimeout(() => row.remove(), 500);
                } else {
                    alert("Error: " + data.data.message);
                    e.target.textContent = 'Save & Create';
                    e.target.disabled = false;
                }
            })
            .catch(err => {
                alert("An error occurred.");
                console.error(err);
                e.target.textContent = 'Save & Create';
                e.target.disabled = false;
            });
        });
    });

    // Manual Updates Check
    const checkUpdateBtn = document.getElementById('kc-check-updates-btn');
    if (checkUpdateBtn) {
        checkUpdateBtn.addEventListener('click', () => {
            const resultArea = document.getElementById('kc-update-result');
            const statusText = document.getElementById('kc-update-status-text');
            const messageText = document.getElementById('kc-update-message-text');

            checkUpdateBtn.disabled = true;
            checkUpdateBtn.textContent = 'Checking...';
            resultArea.style.display = 'none';

            const formData = new FormData();
            formData.append('action', 'kc_check_updates');
            formData.append('nonce', kcObject.nonce);

            fetch(kcObject.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                checkUpdateBtn.disabled = false;
                checkUpdateBtn.textContent = 'Check for Updates';
                resultArea.style.display = 'block';

                if (data.success) {
                    if (data.data.update_available) {
                        statusText.textContent = 'Update Available!';
                        statusText.style.color = '#166534';
                        resultArea.style.backgroundColor = '#dcfce7';
                        resultArea.style.borderColor = '#166534';
                        messageText.textContent = data.data.message + ' Go to the Plugins page to update.';
                    } else {
                        statusText.textContent = 'Up to Date';
                        statusText.style.color = '#333';
                        resultArea.style.backgroundColor = '#f9f9f9';
                        resultArea.style.borderColor = '#eee';
                        messageText.textContent = data.data.message;
                    }
                } else {
                    statusText.textContent = 'Error';
                    statusText.style.color = '#dc2626';
                    resultArea.style.backgroundColor = '#fef2f2';
                    resultArea.style.borderColor = '#dc2626';
                    messageText.textContent = data.data.message;
                }
            })
            .catch(err => {
                checkUpdateBtn.disabled = false;
                checkUpdateBtn.textContent = 'Check for Updates';
                alert("Failed to check for updates.");
                console.error(err);
            });
        });
    }
});
