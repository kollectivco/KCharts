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

    // 3-Step Upload Ingestion Flow
    const uploadForm = document.getElementById('kc-upload-form');
    const previewModal = document.getElementById('kc-upload-preview-modal');
    const resultModal = document.getElementById('kc-upload-result-modal');
    const previewContent = document.getElementById('kc-preview-content');
    const confirmBtn = document.getElementById('kc-confirm-import');
    const cancelBtn = document.getElementById('kc-cancel-upload');
    const fileInput = document.getElementById('chart_file');
    const dropZone = document.getElementById('kc-drop-zone');
    const fileDisplay = document.getElementById('kc-file-display');

    if (dropZone && fileInput) {
        dropZone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                fileDisplay.innerHTML = `<strong style="color:#000;">${fileInput.files[0].name}</strong> selected.`;
            }
        });
    }

    if (uploadForm) {
        uploadForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const btn = uploadForm.querySelector('button[type="submit"]');
            btn.textContent = "Analyzing File...";
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
                btn.textContent = "Upload & Preview →";
                btn.disabled = false;
                
                if(data.success) {
                    const stats = data.data;
                    showUploadResult(stats);
                } else {
                    alert("Error: " + data.data.message);
                }
            })
            .catch(err => {
                alert("An error occurred");
                console.error(err);
                btn.disabled = false;
            });
        });
    }

    function showUploadResult(stats) {
        const resultArea = document.getElementById('kc-result-content');
        resultModal.style.display = 'flex';
        resultArea.innerHTML = `
            <div style="font-size: 4rem; margin-bottom: 20px;">✅</div>
            <h2 style="margin-bottom: 10px;">Ingestion Complete</h2>
            <p style="color:#666; margin-bottom:24px;">Source file processed successfully via <strong>${stats.parser_detected}</strong> profile.</p>
            
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; text-align:left; background:#f8fafc; padding:20px; border-radius:12px; border:1px solid #e2e8f0;">
                <div>
                    <label style="font-size:0.7rem; color:#64748b; text-transform:uppercase; font-weight:700;">Rows Processed</label>
                    <div style="font-size:1.5rem; font-weight:800;">${stats.total_rows}</div>
                </div>
                <div>
                    <label style="font-size:0.7rem; color:#64748b; text-transform:uppercase; font-weight:700;">Errors/Review</label>
                    <div style="font-size:1.5rem; font-weight:800; color:${stats.review_rows > 0 ? '#dc2626' : '#166534'};">${stats.review_rows}</div>
                </div>
                <div style="grid-column: span 2; border-top:1px solid #e2e8f0; padding-top:10px; margin-top:5px;">
                    <label style="font-size:0.7rem; color:#64748b; text-transform:uppercase; font-weight:700;">New Records Created</label>
                    <div style="font-size:0.95rem; margin-top:5px;">
                        <strong>${stats.artists_created}</strong> Artists, <strong>${stats.tracks_created}</strong> Tracks
                    </div>
                </div>
            </div>
            ${stats.review_rows > 0 ? `
                <div style="margin-top:20px; background:#fff7ed; border:1px solid #ffedd5; padding:12px; border-radius:8px; font-size:0.85rem; color:#9a3412;">
                    <strong>Heads up!</strong> ${stats.review_rows} rows require manual artist matching in the Review queue.
                </div>
            ` : ''}
        `;
    }

    // Review Suggestion Logic
    document.querySelectorAll('.kc-apply-suggestion').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const row = e.target.closest('tr');
            const artistId = e.target.getAttribute('data-artist-id');
            const select = row.querySelector('.kc-resolve-artist');
            select.value = artistId;
            e.target.textContent = 'Applied';
            e.target.style.background = '#dcfce7';
            e.target.style.borderColor = '#166534';
            e.target.style.color = '#166534';
        });
    });

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
                    setTimeout(() => {
                        row.remove();
                        // Update UI stats if they exist in the summary card
                        const reviewCountEl = document.querySelector('.kc-stat-review');
                        if (reviewCountEl) {
                            const current = parseInt(reviewCountEl.textContent);
                            reviewCountEl.textContent = Math.max(0, current - 1);
                        }
                        // If no more rows, reload to show empty state
                        if (document.querySelectorAll('.kc-table tbody tr').length === 0) {
                            window.location.reload();
                        }
                    }, 500);
                } else {
                    alert("Error: " + data.data.message);
                    e.target.textContent = 'Save & Resolve';
                    e.target.disabled = false;
                }
            })
            .catch(err => {
                alert("An error occurred.");
                console.error(err);
                e.target.textContent = 'Save & Resolve';
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
