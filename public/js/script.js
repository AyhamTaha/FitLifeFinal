// Filter & search on exercises page (search + difficulty only)
document.addEventListener('DOMContentLoaded', function () {
  const searchInput      = document.getElementById('exerciseSearch');
  const difficultySelect = document.getElementById('difficultyFilter');
  const cards            = document.querySelectorAll('#exerciseGrid .exercise-card');

  // If not on exercises page, exit
  if (!cards.length || !searchInput || !difficultySelect) return;

  function applyFilters() {
    const search = (searchInput.value || '').toLowerCase();
    const diff   = (difficultySelect.value || ''); // "", Beginner, Intermediate, Advanced

    cards.forEach((card) => {
      const titleElem = card.querySelector('h3');
      const title     = titleElem ? titleElem.textContent.toLowerCase() : '';

      const cardDiff  = card.dataset.difficulty || '';

      const matchesSearch = title.includes(search);
      const matchesDiff   = !diff || cardDiff === diff;

      const show = matchesSearch && matchesDiff;
      card.style.display = show ? '' : 'none';
    });
  }

  // Event listeners
  searchInput.addEventListener('input', applyFilters);
  difficultySelect.addEventListener('change', applyFilters);

  // Initial call on load
  applyFilters();
});

// ===== Program filters on programs.php =====
document.addEventListener('DOMContentLoaded', function () {
  const programGrid = document.getElementById('programGrid');
  if (!programGrid) return; // not on programs page

  const goalFilter  = document.getElementById('goalFilter');
  const levelFilter = document.getElementById('levelFilter');
  const cards       = programGrid.querySelectorAll('.program-card');

  function applyProgramFilters() {
    const goal  = goalFilter.value;
    const level = levelFilter.value;

    cards.forEach(card => {
      const cardGoal  = card.dataset.goal;
      const cardLevel = card.dataset.level;

      const matchGoal  = !goal  || cardGoal  === goal;
      const matchLevel = !level || cardLevel === level;

      card.style.display = (matchGoal && matchLevel) ? '' : 'none';
    });
  }

  goalFilter.addEventListener('change', applyProgramFilters);
  levelFilter.addEventListener('change', applyProgramFilters);

  applyProgramFilters();
});
// === Programs page filters ===
document.addEventListener('DOMContentLoaded', function () {
  const goalSelect  = document.getElementById('programGoalFilter');
  const levelSelect = document.getElementById('programLevelFilter');
  const cards       = document.querySelectorAll('#programGrid .program-card');

  // If we're not on programs page, stop
  if (!goalSelect || !levelSelect || !cards.length) return;

  function applyProgramFilters() {
    const goal  = goalSelect.value.toLowerCase();   // '' or 'muscle gain'
    const level = levelSelect.value.toLowerCase();  // '' or 'beginner'...

    cards.forEach(card => {
      const cardGoal  = (card.dataset.goal  || '').toLowerCase();
      const cardLevel = (card.dataset.level || '').toLowerCase();

      const matchesGoal  = !goal  || cardGoal  === goal;
      const matchesLevel = !level || cardLevel === level;

      card.style.display = (matchesGoal && matchesLevel) ? '' : 'none';
    });
  }

  goalSelect.addEventListener('change', applyProgramFilters);
  levelSelect.addEventListener('change', applyProgramFilters);

  // Initial run
  applyProgramFilters();
});
// === Programs page: search + filters ===
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('programSearch');
  const goalSelect  = document.getElementById('goalFilter');
  const levelSelect = document.getElementById('levelFilter');
  const cards       = document.querySelectorAll('#programGrid .program-card');

  // If not on programs page, exit
  if (!cards.length || !goalSelect || !levelSelect || !searchInput) return;

  function applyProgramFilters() {
    const search = searchInput.value.toLowerCase();
    const goal   = (goalSelect.value || '').toLowerCase();   // muscle / fat-loss / ...
    const level  = (levelSelect.value || '').toLowerCase();  // beginner / ...

    cards.forEach(card => {
      const cardGoal  = (card.dataset.goal  || '').toLowerCase();
      const cardLevel = (card.dataset.level || '').toLowerCase();

      const titleElem = card.querySelector('h3');
      const descElem  = card.querySelector('.program-description');

      const title = titleElem ? titleElem.textContent.toLowerCase() : '';
      const desc  = descElem ? descElem.textContent.toLowerCase() : '';

      const matchesSearch =
        !search || title.includes(search) || desc.includes(search);

      const matchesGoal  = !goal  || cardGoal  === goal;
      const matchesLevel = !level || cardLevel === level;

      const show = matchesSearch && matchesGoal && matchesLevel;
      card.style.display = show ? '' : 'none';
    });
  }

  // Event listeners
  searchInput.addEventListener('input', applyProgramFilters);
  goalSelect.addEventListener('change', applyProgramFilters);
  levelSelect.addEventListener('change', applyProgramFilters);

  // Initial run
  applyProgramFilters();
});
