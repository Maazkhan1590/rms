// Publications page specific functionality - will be handled by Laravel backend

document.addEventListener('DOMContentLoaded', () => {
    // Modal functionality
    const modal = document.getElementById('publication-modal');
    const modalClose = document.getElementById('modal-close');
    const modalBody = document.getElementById('modal-body');
    const viewButtons = document.querySelectorAll('.view-publication-btn');
    
    // Open modal and load publication details
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const publicationId = this.getAttribute('data-id');
            loadPublicationDetails(publicationId);
        });
    });

    // Close modal handlers
    if (modalClose) {
        modalClose.addEventListener('click', closeModal);
    }

    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    function loadPublicationDetails(id) {
        if (!modal || !modalBody) return;
        
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        modalBody.innerHTML = `
            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--accent-color);"></i>
                <p>Loading publication details...</p>
            </div>
        `;

        fetch(`/publications/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }
                return response.text().then(html => ({ html }));
            })
            .then(data => {
                if (data.html) {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data.html, 'text/html');
                    const content = doc.querySelector('.publication-detail-content') || doc.body;
                    modalBody.innerHTML = `<div class="publication-detail-modal">${content.innerHTML}</div>`;
                } else if (data.publication) {
                    // Use JSON data to build modal content
                    const pub = data.publication;
                    modalBody.innerHTML = buildModalContent(pub);
                }
            })
            .catch(error => {
                console.error('Error loading publication:', error);
                modalBody.innerHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #ef4444; margin-bottom: 1rem;"></i>
                        <p>Error loading publication details. Please try again.</p>
                        <button class="btn btn-primary" onclick="closeModal()">Close</button>
                    </div>
                `;
            });
    }

    function buildModalContent(pub) {
        const authors = pub.authors && Array.isArray(pub.authors) 
            ? pub.authors.map(a => a.name || a).join(', ')
            : (pub.submitter?.name || 'Anonymous');
        
        return `
            <div class="publication-detail-modal">
                <div class="publication-detail-header">
                    <span class="publication-detail-category">${(pub.publication_type || 'Publication').toUpperCase().replace('_', ' ')}</span>
                    <h2>${pub.title}</h2>
                    <div class="publication-detail-authors">
                        <i class="fas fa-user-edit"></i> ${authors}
                    </div>
                </div>
                <div class="publication-detail-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Published: ${pub.publication_year || 'N/A'}</span>
                    </div>
                    ${pub.journal_name ? `<div class="meta-item"><i class="fas fa-book"></i> <span>Journal: ${pub.journal_name}</span></div>` : ''}
                    ${pub.conference_name ? `<div class="meta-item"><i class="fas fa-users"></i> <span>Conference: ${pub.conference_name}</span></div>` : ''}
                    ${pub.doi ? `<div class="meta-item"><i class="fas fa-hashtag"></i> <span>DOI: <a href="https://doi.org/${pub.doi}" target="_blank">${pub.doi}</a></span></div>` : ''}
                    ${pub.publisher ? `<div class="meta-item"><i class="fas fa-building"></i> <span>Publisher: ${pub.publisher}</span></div>` : ''}
                    ${pub.isbn ? `<div class="meta-item"><i class="fas fa-barcode"></i> <span>ISBN: ${pub.isbn}</span></div>` : ''}
                </div>
                ${pub.abstract ? `<div class="publication-detail-abstract"><h3><i class="fas fa-file-alt"></i> Abstract</h3><p>${pub.abstract}</p></div>` : ''}
                ${pub.authors && Array.isArray(pub.authors) && pub.authors.length > 0 ? `
                    <div class="publication-detail-keywords">
                        <h3><i class="fas fa-users"></i> Authors</h3>
                        <div class="keywords-list">
                            ${pub.authors.map(a => `<span class="keyword">${a.name || a}</span>`).join('')}
                        </div>
                    </div>
                ` : ''}
                <div class="publication-detail-actions">
                    ${pub.published_link ? `<a href="${pub.published_link}" target="_blank" class="btn btn-primary"><i class="fas fa-external-link-alt"></i> View Publication</a>` : ''}
                    ${pub.proceedings_link ? `<a href="${pub.proceedings_link}" target="_blank" class="btn btn-outline"><i class="fas fa-file-pdf"></i> View Proceedings</a>` : ''}
                    <a href="/publications/${pub.id}" class="btn btn-outline"><i class="fas fa-external-link-alt"></i> Full Page View</a>
                </div>
            </div>
        `;
    }

    function closeModal() {
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    window.closeModal = closeModal;
});
