// Enhanced Main JavaScript with animations

// DOM Elements
const menuToggle = document.querySelector('.menu-toggle');
const navMenu = document.querySelector('.nav-menu');
const searchBtn = document.getElementById('search-btn');
const searchInput = document.getElementById('search-input');
const publicationsContainer = document.getElementById('publications-container');

// Sample Publications Data
const publicationsData = [
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
    }
];

// Toggle Mobile Navigation
if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        menuToggle.classList.toggle('active');
        document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
    });
}

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
        navMenu.classList.remove('active');
        menuToggle.classList.remove('active');
        document.body.style.overflow = '';
    });
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (!navMenu.contains(e.target) && !menuToggle.contains(e.target)) {
        navMenu.classList.remove('active');
        menuToggle.classList.remove('active');
        document.body.style.overflow = '';
    }
});

// Generate Elegant Publication Card HTML
function generatePublicationCard(publication) {
    const date = new Date(publication.date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    const categoryMap = {
        'science': 'Natural Sciences',
        'engineering': 'Engineering',
        'medicine': 'Medicine',
        'social': 'Social Sciences',
        'humanities': 'Humanities'
    };

    return `
        <div class="publication-card" data-category="${publication.category}">
            <div class="publication-header">
                <span class="publication-category">${categoryMap[publication.category] || 'Research'}</span>
                <h3 class="publication-title">${publication.title}</h3>
                <div class="publication-authors">
                    <i class="fas fa-user-edit"></i>
                    ${publication.authors}
                </div>
            </div>
            <div class="publication-body">
                <p class="publication-abstract">${publication.abstract.substring(0, 200)}...</p>
            </div>
            <div class="publication-footer">
                <div class="publication-meta">
                    <div class="publication-date">
                        <i class="far fa-calendar"></i> ${date}
                    </div>
                </div>
                <div class="publication-actions">
                    <button class="btn btn-outline view-publication" data-id="${publication.id}">
                        <i class="fas fa-eye"></i> View Details
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Load Publications with Animation
function loadPublications(publications = publicationsData) {
    if (!publicationsContainer) return;

    publicationsContainer.innerHTML = '';

    if (publications.length === 0) {
        publicationsContainer.innerHTML = `
            <div class="no-results" style="grid-column: 1 / -1; text-align: center; padding: 4rem;">
                <i class="fas fa-search" style="font-size: 4rem; color: var(--text-lighter); margin-bottom: 1.5rem; opacity: 0.5;"></i>
                <h3 style="color: var(--text-color); margin-bottom: 0.5rem;">No publications found</h3>
                <p style="color: var(--text-lighter); max-width: 400px; margin: 0 auto;">Try adjusting your search or filter criteria</p>
            </div>
        `;
        return;
    }

    publications.forEach((publication, index) => {
        publicationsContainer.innerHTML += generatePublicationCard(publication);
    });

    // Add event listeners to view buttons with animation
    document.querySelectorAll('.view-publication').forEach((button, index) => {
        button.addEventListener('click', function() {
            const id = parseInt(this.getAttribute('data-id'));
            viewPublication(id);
        });
    });

    // Animate cards on load
    setTimeout(() => {
        document.querySelectorAll('.publication-card').forEach((card, index) => {
            card.style.animation = `fadeInUp 0.6s ease ${index * 0.1}s both`;
        });
    }, 100);
}

// View Publication Details
function viewPublication(id) {
    const publication = publicationsData.find(p => p.id === id);

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
            <div class="publication-detail-header">
                <div class="publication-detail-category">${publication.category.charAt(0).toUpperCase() + publication.category.slice(1)}</div>
                <h2>${publication.title}</h2>
                <div class="publication-detail-authors">${publication.authors}</div>
            </div>
            <div class="publication-detail-meta">
                <div class="meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Published: ${date}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-hashtag"></i>
                    <span>DOI: 10.1234/arxiv.${publication.id}${publication.date.replace(/-/g, '')}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-download"></i>
                    <span>Downloads: 1,247</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-quote-left"></i>
                    <span>Citations: 89</span>
                </div>
            </div>
            <div class="publication-detail-abstract">
                <h3><i class="fas fa-file-alt"></i> Abstract</h3>
                <p>${publication.abstract}</p>
            </div>
            <div class="publication-detail-keywords">
                <h3><i class="fas fa-tags"></i> Keywords</h3>
                <div class="keywords-list">
                    ${keywordsHTML}
                </div>
            </div>
            <div class="publication-detail-actions">
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download PDF
                </a>
                <a href="#" class="btn btn-outline">
                    <i class="fas fa-quote-left"></i> Cite This Paper
                </a>
                <a href="#" class="btn btn-outline">
                    <i class="fas fa-share-alt"></i> Share
                </a>
            </div>
        </div>
    `;

    // Check if we're on publications page (has modal)
    const modal = document.getElementById('publication-modal');
    if (modal) {
        document.getElementById('modal-body').innerHTML = modalHTML;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Close modal when clicking X
        document.getElementById('modal-close').addEventListener('click', () => {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        });

        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    } else {
        // For home page, redirect to publications page with hash
        window.location.href = `publications.html#publication-${id}`;
    }
}

// Search and Filter Functions
function searchPublications() {
    if (!searchInput) return;

    const searchTerm = searchInput.value.toLowerCase();
    const categoryFilter = document.getElementById('filter-category') ?
        document.getElementById('filter-category').value : 'all';

    const filteredPublications = publicationsData.filter(publication => {
        const matchesSearch = searchTerm === '' ||
            publication.title.toLowerCase().includes(searchTerm) ||
            publication.authors.toLowerCase().includes(searchTerm) ||
            publication.abstract.toLowerCase().includes(searchTerm) ||
            publication.keywords.some(keyword => keyword.toLowerCase().includes(searchTerm));

        const matchesCategory = categoryFilter === 'all' || publication.category === categoryFilter;

        return matchesSearch && matchesCategory;
    });

    loadPublications(filteredPublications);
}

// Animate stats numbers
function animateStats() {
    const statNumbers = document.querySelectorAll('.stat-number');

    statNumbers.forEach(stat => {
        const target = parseInt(stat.getAttribute('data-count'));
        const increment = target / 100;
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            stat.textContent = Math.floor(current).toLocaleString();
        }, 20);
    });
}

// Quick filters functionality
function setupQuickFilters() {
    const quickFilters = document.querySelectorAll('.quick-filter');

    quickFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            // Remove active class from all filters
            quickFilters.forEach(f => f.classList.remove('active'));

            // Add active class to clicked filter
            this.classList.add('active');

            // Get filter type
            const filterType = this.textContent.toLowerCase();

            // Apply filter
            let filteredPublications = [...publicationsData];

            switch(filterType) {
                case 'trending':
                    // Simulate trending filter
                    filteredPublications = filteredPublications.sort(() => Math.random() - 0.5).slice(0, 3);
                    break;
                case 'recently added':
                    filteredPublications = filteredPublications.sort((a, b) =>
                        new Date(b.date) - new Date(a.date)
                    );
                    break;
                case 'most cited':
                    // Simulate citation count
                    filteredPublications = filteredPublications.sort(() => Math.random() - 0.5);
                    break;
            }

            loadPublications(filteredPublications);
        });
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Load publications on home page
    if (publicationsContainer) {
        loadPublications();
    }

    // Animate stats
    if (document.querySelector('.stat-number')) {
        // Wait for stats section to be in view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateStats();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            observer.observe(statsSection);
        }
    }

    // Add search functionality
    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', searchPublications);
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                searchPublications();
            }
        });
    }

    // Add category filter change listener
    const categoryFilter = document.getElementById('filter-category');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', searchPublications);
    }

    // Add year filter change listener
    const yearFilter = document.getElementById('filter-year');
    if (yearFilter) {
        yearFilter.addEventListener('change', searchPublications);
    }

    // Setup quick filters
    setupQuickFilters();

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');

            // Skip if it's just "#"
            if (href === '#') return;

            // Check if it's a same-page anchor
            if (href.startsWith('#') && document.querySelector(href)) {
                e.preventDefault();
                const target = document.querySelector(href);
                const offset = 100; // Height of fixed navbar
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Add CSS for animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .publication-detail-header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-light);
        }
        
        .publication-detail-category {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent-color) 0%, #52d7c2 100%);
            color: var(--primary-color);
            padding: 0.4rem 1.2rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }
        
        .publication-detail-authors {
            color: var(--text-lighter);
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }
        
        .publication-detail-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-light);
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-light);
            font-size: 0.95rem;
        }
        
        .meta-item i {
            color: var(--accent-color);
            width: 20px;
        }
        
        .publication-detail-abstract h3,
        .publication-detail-keywords h3 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        
        .publication-detail-abstract h3 i,
        .publication-detail-keywords h3 i {
            color: var(--accent-color);
        }
        
        .publication-detail-abstract p {
            line-height: 1.8;
            color: var(--text-color);
            font-size: 1.05rem;
        }
        
        .keywords-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .keyword {
            background: linear-gradient(135deg, var(--light-color) 0%, #f1f5f9 100%);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            color: var(--text-color);
            border: 1px solid var(--border-light);
        }
        
        .publication-detail-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
    `;
    document.head.appendChild(style);
});