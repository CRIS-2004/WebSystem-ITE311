<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Search Results for "<?= esc($searchTerm) ?>"</h2>
            
            <!-- Back to Courses Link -->
            <div class="mb-4">
                <a href="/courses" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to All Courses
                </a>
            </div>
            
            <!-- Search Form -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form id="searchForm" class="d-flex">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" 
                                   placeholder="Search courses..." 
                                   name="search_term" 
                                   value="<?= esc($searchTerm) ?>">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Results -->
            <div class="row">
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?= esc($course['course_name']) ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?= esc($course['course_code']) ?></h6>
                                    <p class="card-text"><?= esc($course['description']) ?></p>
                                    <a href="/courses/view/<?= $course['course_id'] ?>" class="btn btn-primary">View Course</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">No courses found matching your search.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Server-side search with AJAX
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        var searchTerm = $('#searchInput').val();
        if (searchTerm.trim() === '') {
            window.location.href = '/courses';
            return;
        }
        window.location.href = '/courses/search?search_term=' + encodeURIComponent(searchTerm);
    });
});
</script>

<?= $this->endSection() ?>
