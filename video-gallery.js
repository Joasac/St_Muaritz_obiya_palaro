// Video Gallery JavaScript

// Sample video data - replace with your actual video files
const videoData = [
    {
        id: 1,
        title: "Our Church History",
        description: "Learn about the founding and growth of St. Moritz Catholic Church over the decades.",
        category: "history",
        thumbnail: "https://images.unsplash.com/photo-1507692049790-de58290a4334?w=400&h=250&fit=crop",
        videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4", // Replace with actual video URL
        duration: "5:30",
        uploadDate: "2023-10-15"
    },
    {
        id: 2,
        title: "Annual Harvest Festival 2023",
        description: "Highlights from our biggest community celebration featuring traditional music, food, and fellowship.",
        category: "events",
        thumbnail: "https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=400&h=250&fit=crop",
        videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_2mb.mp4", // Replace with actual video URL
        duration: "8:15",
        uploadDate: "2023-11-25"
    },
    {
        id: 3,
        title: "Health Center in Action",
        description: "See how our health center provides essential medical care to the community.",
        category: "ministries",
        thumbnail: "https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=400&h=250&fit=crop",
        videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4", // Replace with actual video URL
        duration: "6:45",
        uploadDate: "2023-09-20"
    },
    {
        id: 4,
        title: "Maria's Story - Education Impact",
        description: "A mother shares how education sponsorship changed her children's lives.",
        category: "testimonials",
        thumbnail: "https://images.unsplash.com/photo-1544027993-37dbfe43562a?w=400&h=250&fit=crop",
        videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4", // Replace with actual video URL
        duration: "4:20",
        uploadDate: "2023-08-10"
    },
    {
        id: 5,
        title: "Sunday Morning Service",
        description: "Experience our uplifting Sunday worship service with choir and community.",
        category: "worship",
        thumbnail: "https://images.unsplash.com/photo-1438232992991-995b7058bbb3?w=400&h=250&fit=crop",
        videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_2mb.mp4", // Replace with actual video URL
        duration: "45:00",
        uploadDate: "2023-11-05"
    },
    {
        id: 6,
        title: "Youth Ministry Activities",
        description: "Young people engaging in sports, discussions, and community service projects.",
        category: "ministries",
        thumbnail: "https://images.unsplash.com/photo-1529390079861-591de354faf5?w=400&h=250&fit=crop",
        videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4", // Replace with actual video URL
        duration: "7:30",
        uploadDate: "2023-10-28"
    },
    {
        id: 7,
        title: "Christmas Celebration 2022",
        description: "Relive the joy and wonder of our Christmas service and nativity play.",
        category: "events",
        thumbnail: "https://images.unsplash.com/photo-1512389142860-9c449e58a543?w=400&h=250&fit=crop",
        videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_2mb.mp4", // Replace with actual video URL
        duration: "12:40",
        uploadDate: "2022-12-25"
    },
    {
        id: 8,
        title: "Agriculture Training Program",
        description: "Farmers learning modern techniques and sustainable practices.",
        category: "ministries",
        thumbnail: "https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&h=250&fit=crop",
        videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4", // Replace with actual video URL
        duration: "9:15",
        uploadDate: "2023-07-15"
    },
    {
        id: 9,
        title: "John's Farming Success",
        description: "A farmer shares how agricultural training transformed his harvest and income.",
        category: "testimonials",
        thumbnail: "https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=400&h=250&fit=crop",
        videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4", // Replace with actual video URL
        duration: "5:55",
        uploadDate: "2023-09-08"
    },
    {
        id: 10,
        title: "Building Our Future Together",
        description: "The story of our church expansion and community growth over the years.",
        category: "history",
        thumbnail: "https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?w=400&h=250&fit=crop",
        videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_2mb.mp4", // Replace with actual video URL
        duration: "11:20",
        uploadDate: "2023-06-30"
    }
];

// DOM Elements
const videosGrid = document.getElementById('videosGrid');
const categoryButtons = document.querySelectorAll('.category-btn');
const videoModal = document.getElementById('videoModal');
const modalVideo = document.getElementById('modalVideo');
const modalTitle = document.getElementById('modalTitle');
const modalDescription = document.getElementById('modalDescription');
const modalClose = document.getElementById('modalClose');

// Current filter
let currentFilter = 'all';

// Initialize the gallery
document.addEventListener('DOMContentLoaded', function() {
    renderVideos(videoData);
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Category filter buttons
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter videos
            currentFilter = this.getAttribute('data-category');
            filterVideos();
        });
    });

    // Modal close events
    modalClose.addEventListener('click', closeModal);
    videoModal.addEventListener('click', function(e) {
        if (e.target === videoModal) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
}

// Render videos in the grid
function renderVideos(videos) {
    videosGrid.innerHTML = '';
    
    videos.forEach(video => {
        const videoCard = createVideoCard(video);
        videosGrid.appendChild(videoCard);
    });
}

// Create individual video card
function createVideoCard(video) {
    const card = document.createElement('div');
    card.className = 'video-card';
    card.setAttribute('data-category', video.category);
    
    card.innerHTML = `
        <div class="video-thumbnail" style="background-image: url('${video.thumbnail}')">
            <div class="play-btn">
                <i class="fas fa-play"></i>
            </div>
        </div>
        <div class="video-info">
            <span class="video-category">${getCategoryName(video.category)}</span>
            <h3 class="video-title">${video.title}</h3>
            <p class="video-description">${video.description}</p>
            <div class="video-meta">
                <span><i class="fas fa-clock"></i> ${video.duration}</span>
                <span><i class="fas fa-calendar"></i> ${formatDate(video.uploadDate)}</span>
            </div>
        </div>
    `;

    // Add click event to open modal
    card.addEventListener('click', function() {
        openModal(video);
    });

    return card;
}

// Filter videos by category
function filterVideos() {
    const filteredVideos = currentFilter === 'all' 
        ? videoData 
        : videoData.filter(video => video.category === currentFilter);
    
    renderVideos(filteredVideos);
}

// Open video modal
function openModal(video) {
    modalTitle.textContent = video.title;
    modalDescription.textContent = video.description;
    modalVideo.src = video.videoUrl;
    videoModal.classList.add('active');
    
    // Prevent body scroll when modal is open
    document.body.style.overflow = 'hidden';
}

// Close video modal
function closeModal() {
    videoModal.classList.remove('active');
    modalVideo.pause();
    modalVideo.src = '';
    
    // Restore body scroll
    document.body.style.overflow = '';
}

// Get display name for category
function getCategoryName(category) {
    const categoryNames = {
        'history': 'Church History',
        'events': 'Events & Celebrations',
        'ministries': 'Ministries',
        'testimonials': 'Community Stories',
        'worship': 'Worship Services'
    };
    return categoryNames[category] || category;
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

// Search functionality (optional enhancement)
function searchVideos(searchTerm) {
    const filteredVideos = videoData.filter(video => 
        video.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
        video.description.toLowerCase().includes(searchTerm.toLowerCase())
    );
    renderVideos(filteredVideos);
}

// Load more videos (for pagination - optional enhancement)
function loadMoreVideos() {
    // Implementation for loading additional videos
    console.log('Loading more videos...');
}
