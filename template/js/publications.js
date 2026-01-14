// Publications page specific functionality

document.addEventListener('DOMContentLoaded', () => {
    const allPublicationsContainer = document.getElementById('all-publications-container');
    const searchInput = document.getElementById('pub-search-input');
    const categoryFilter = document.getElementById('pub-filter-category');
    const yearFilter = document.getElementById('pub-filter-year');
    const sortFilter = document.getElementById('pub-filter-sort');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const currentPageSpan = document.getElementById('current-page');
    const totalPagesSpan = document.getElementById('total-pages');

    // Extended publications data for publications page
    const allPublicationsData = [
        // Existing publications
        {
            id: 1,
            title: "Advances in Quantum Computing Algorithms",
            authors: "Dr. Jane Smith, Dr. Robert Chen, Prof. Maria Gonzalez",
            abstract: "This paper explores recent breakthroughs in quantum algorithm development, focusing on optimization techniques that significantly reduce computational time for complex problems. Our findings demonstrate a 47% improvement in processing speed for factorization algorithms.",
            date: "2024-03-15",
            category: "science",
            keywords: ["Quantum Computing", "Algorithms", "Optimization", "Computer Science"]
        },
        {
            id: 2,
            title: "Machine Learning Applications in Early Cancer Detection",
            authors: "Dr. Alan Turing, Dr. Katherine Johnson, Prof. Grace Hopper",
            abstract: "We present a novel deep learning framework for early-stage cancer detection using medical imaging data. The model achieves 94.3% accuracy in identifying malignant tumors, outperforming traditional diagnostic methods.",
            date: "2024-02-28",
            category: "medicine",
            keywords: ["Machine Learning", "Cancer Detection", "Medical Imaging", "AI in Healthcare"]
        },
        {
            id: 3,
            title: "Sustainable Energy Solutions for Urban Environments",
            authors: "Prof. David Wilson, Dr. Sarah Miller, Dr. James Brown",
            abstract: "This research evaluates the feasibility of integrated renewable energy systems in metropolitan areas. Our case study of New York City demonstrates potential for 35% reduction in carbon emissions through smart grid implementation.",
            date: "2024-01-20",
            category: "engineering",
            keywords: ["Renewable Energy", "Sustainability", "Urban Planning", "Smart Grid"]
        },
        {
            id: 4,
            title: "The Impact of Digital Media on Adolescent Mental Health",
            authors: "Dr. Lisa Wang, Prof. Michael Rodriguez, Dr. Emily Chen",
            abstract: "Longitudinal study examining the correlation between social media usage patterns and mental health indicators in adolescents. Results indicate significant relationships between screen time and anxiety levels.",
            date: "2023-11-10",
            category: "social",
            keywords: ["Mental Health", "Digital Media", "Adolescents", "Psychology"]
        },
        {
            id: 5,
            title: "Ancient Trade Routes and Cultural Exchange",
            authors: "Prof. Richard Evans, Dr. Sophia Martinez, Dr. Thomas Wright",
            abstract: "Archaeological evidence from newly discovered sites along the Silk Road reveals previously unknown patterns of cultural exchange between Eastern and Western civilizations during the 8th-10th centuries.",
            date: "2023-10-05",
            category: "humanities",
            keywords: ["Archaeology", "Cultural Exchange", "Silk Road", "Ancient History"]
        },
        {
            id: 6,
            title: "Nanotechnology in Drug Delivery Systems",
            authors: "Dr. Olivia Parker, Prof. Benjamin Scott, Dr. Chloe Adams",
            abstract: "Development of nanoparticle-based targeted drug delivery systems showing promising results in preclinical trials for treatment of neurodegenerative diseases with reduced side effects.",
            date: "2023-09-18",
            category: "medicine",
            keywords: ["Nanotechnology", "Drug Delivery", "Neurology", "Pharmaceuticals"]
        },
        // Additional publications
        {
            id: 7,
            title: "Climate Change Impacts on Coastal Ecosystems",
            authors: "Dr. Rachel Green, Prof. Mark Taylor, Dr. Susan Lee",
            abstract: "Analysis of 20-year coastal monitoring data reveals significant changes in biodiversity and ecosystem services due to rising sea levels and temperature changes.",
            date: "2023-08-12",
            category: "science",
            keywords: ["Climate Change", "Ecosystems", "Biodiversity", "Environmental Science"]
        },
        {
            id: 8,
            title: "Blockchain Technology for Secure Voting Systems",
            authors: "Prof. Alex Johnson, Dr. Michelle Carter, Dr. Kevin White",
            abstract: "Implementation of a blockchain-based voting system that ensures transparency, security, and verifiability while maintaining voter anonymity.",
            date: "2023-07-05",
            category: "engineering",
            keywords: ["Blockchain", "Voting Systems", "Security", "Cryptography"]
        },
        {
            id: 9,
            title: "Economic Implications of Universal Basic Income",
            authors: "Dr. Richard Brown, Prof. Amanda Wilson, Dr. Thomas Clark",
            abstract: "Comprehensive study of UBI pilot programs across different countries and their impacts on employment, poverty reduction, and economic growth.",
            date: "2023-06-20",
            category: "social",
            keywords: ["Economics", "UBI", "Social Policy", "Poverty"]
        },
        {
            id: 10,
            title: "Artificial Intelligence in Creative Writing",
            authors: "Dr. Emma Watson, Prof. Daniel Harris, Dr. Sophia Chen",
            abstract: "Evaluation of AI-generated literary works and their reception by readers compared to human-authored works.",
            date: "2023-05-14",
            category: "humanities",
            keywords: ["Artificial Intelligence", "Creative Writing", "Literature", "Digital Humanities"]
        },
        {
            id: 11,
            title: "Gene Therapy for Inherited Metabolic Disorders",
            authors: "Prof. Robert Miller, Dr. Jennifer Adams, Dr. Paul Davis",
            abstract: "Clinical trial results showing promising outcomes for patients with rare metabolic disorders using targeted gene therapy approaches.",
            date: "2023-04-08",
            category: "medicine",
            keywords: ["Gene Therapy", "Metabolic Disorders", "Clinical Trials", "Genetics"]
        },
        {
            id: 12,
            title: "Advanced Materials for Space Exploration",
            authors: "Dr. Michael Scott, Prof. Lisa Brown, Dr. Andrew Wilson",
            abstract: "Development of lightweight, radiation-resistant materials for next-generation spacecraft and space habitats.",
            date: "2023-03-22",
            category: "engineering",
            keywords: ["Materials Science", "Space Exploration", "Aerospace", "Radiation"]
        }
    ];

    // Pagination variables
    let currentPage = 1;
    const publicationsPerPage = 6;
    let filteredPublications = [...allPublicationsData];

    // Initialize
    updatePagination();
    loadAllPublications();

    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', filterPublications);
    }

    // Filter functionality
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterPublications);
    }

    if (yearFilter) {
        yearFilter.addEventListener('change', filterPublications);
    }

    if (sortFilter) {
        sortFilter.addEventListener('change', filterPublications);
    }

    // Pagination functionality
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                updatePagination();
                loadAllPublications();
            }
        });
    }

    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(filteredPublications.length / publicationsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updatePagination();
                loadAllPublications();
            }
        });
    }

    // Check URL for publication ID
    checkUrlForPublication();

    // Functions
    function filterPublications() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const category = categoryFilter ? categoryFilter.value : 'all';
        const year = yearFilter ? yearFilter.value : 'all';
        const sort = sortFilter ? sortFilter.value : 'newest';

        filteredPublications = allPublicationsData.filter(publication => {
            // Search filter
            const matchesSearch = searchTerm === '' ||
                publication.title.toLowerCase().includes(searchTerm) ||
                publication.authors.toLowerCase().includes(searchTerm) ||
                publication.abstract.toLowerCase().includes(searchTerm) ||
                publication.keywords.some(keyword => keyword.toLowerCase().includes(searchTerm));

            // Category filter
            const matchesCategory = category === 'all' || publication.category === category;

            // Year filter
            const publicationYear = new Date(publication.date).getFullYear().toString();
            const matchesYear = year === 'all' || publicationYear === year;

            return matchesSearch && matchesCategory && matchesYear;
        });

        // Sort publications
        sortPublications(filteredPublications, sort);

        // Reset to first page
        currentPage = 1;
        updatePagination();
        loadAllPublications();
    }

    function sortPublications(publications, sortBy) {
        switch(sortBy) {
            case 'newest':
                publications.sort((a, b) => new Date(b.date) - new Date(a.date));
                break;
            case 'oldest':
                publications.sort((a, b) => new Date(a.date) - new Date(b.date));
                break;
            case 'title':
                publications.sort((a, b) => a.title.localeCompare(b.title));
                break;
            case 'author':
                publications.sort((a, b) => a.authors.localeCompare(b.authors));
                break;
        }
    }

    function loadAllPublications() {
        if (!allPublicationsContainer) return;

        // Calculate start and end indices for current page
        const startIndex = (currentPage - 1) * publicationsPerPage;
        const endIndex = startIndex + publicationsPerPage;
        const currentPublications = filteredPublications.slice(startIndex, endIndex);

        allPublicationsContainer.innerHTML = '';

        if (currentPublications.length === 0) {
            allPublicationsContainer.innerHTML = `
                <div class="no-results" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                    <i class="fas fa-search" style="font-size: 3rem; color: #718096; margin-bottom: 1rem;"></i>
                    <h3 style="color: #2d3748; margin-bottom: 0.5rem;">No publications found</h3>
                    <p style="color: #718096;">Try adjusting your search or filter criteria</p>
                </div>
            `;
            return;
        }

        currentPublications.forEach(publication => {
            const date = new Date(publication.date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            const cardHTML = `
                <div class="publication-card" data-category="${publication.category}">
                    <div class="publication-image" style="background-image: url('https://images.unsplash.com/photo-1554475900-7c0f4a35b8c1?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80');"></div>
                    <div class="publication-content">
                        <h3>${publication.title}</h3>
                        <div class="publication-authors">${publication.authors}</div>
                        <p class="publication-abstract">${publication.abstract.substring(0, 150)}...</p>
                        <div class="publication-meta">
                            <div class="publication-date">${date}</div>
                            <button class="btn btn-secondary view-publication" data-id="${publication.id}">
                                View Publication
                            </button>
                        </div>
                    </div>
                </div>
            `;

            allPublicationsContainer.innerHTML += cardHTML;
        });

        // Add event listeners to view buttons
        document.querySelectorAll('.view-publication').forEach(button => {
            button.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                viewPublication(id);
            });
        });
    }

    function updatePagination() {
        const totalPublications = filteredPublications.length;
        const totalPages = Math.ceil(totalPublications / publicationsPerPage);

        if (currentPageSpan) {
            currentPageSpan.textContent = currentPage;
        }

        if (totalPagesSpan) {
            totalPagesSpan.textContent = totalPages;
        }

        if (prevPageBtn) {
            prevPageBtn.disabled = currentPage === 1;
        }

        if (nextPageBtn) {
            nextPageBtn.disabled = currentPage === totalPages || totalPages === 0;
        }
    }

    function viewPublication(id) {
        const publication = allPublicationsData.find(p => p.id === id);

        if (!publication) return;

        // Format date
        const date = new Date(publication.date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Format keywords
        const keywordsHTML = publication.keywords.map(keyword =>
            `<span class="keyword">${keyword}</span>`
        ).join('');

        // Create modal HTML
        const modalHTML = `
            <div class="publication-detail">
                <h2>${publication.title}</h2>
                <div class="authors">${publication.authors}</div>
                <div class="meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Published: ${date}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-folder"></i>
                        <span>Category: ${publication.category.charAt(0).toUpperCase() + publication.category.slice(1)}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-hashtag"></i>
                        <span>DOI: 10.1234/arxiv.${publication.id}${publication.date.replace(/-/g, '')}</span>
                    </div>
                </div>
                <div class="abstract">
                    <h3>Abstract</h3>
                    <p>${publication.abstract}</p>
                </div>
                <div class="keywords">
                    <h3>Keywords</h3>
                    <div class="keywords-list">
                        ${keywordsHTML}
                    </div>
                </div>
                <div class="publication-actions">
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                    <a href="#" class="btn btn-secondary">
                        <i class="fas fa-quote-left"></i> Cite This Paper
                    </a>
                    <a href="#" class="btn btn-secondary">
                        <i class="fas fa-share-alt"></i> Share
                    </a>
                </div>
            </div>
        `;

        // Show modal
        const modal = document.getElementById('publication-modal');
        if (modal) {
            document.getElementById('modal-body').innerHTML = modalHTML;
            modal.classList.add('active');
        }
    }

    function checkUrlForPublication() {
        // Check if URL has a hash for a specific publication
        const hash = window.location.hash;
        if (hash && hash.startsWith('#publication-')) {
            const id = parseInt(hash.replace('#publication-', ''));
            if (!isNaN(id)) {
                // Wait a bit for DOM to be fully loaded
                setTimeout(() => {
                    viewPublication(id);
                }, 500);
            }
        }
    }
});