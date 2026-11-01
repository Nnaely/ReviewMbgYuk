const schoolSelect = document.getElementById('school_id');
const distributionSelect = document.getElementById('distribution_id');
const distributionStatus = document.getElementById('distribution-status');

async function loadDistributions(selectedId = '') {
    const schoolId = schoolSelect.value;
    distributionSelect.replaceChildren(new Option('Memuat distribusi...', ''));

    if (!schoolId) {
        distributionSelect.replaceChildren(new Option('Pilih sekolah terlebih dahulu', ''));
        distributionStatus.textContent = 'Pilih sekolah untuk memuat distribusi yang berkaitan.';
        return;
    }

    try {
        const url = distributionSelect.dataset.urlTemplate.replace('__SCHOOL_ID__', schoolId);
        const response = await fetch(url, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Permintaan distribusi gagal.');

        const distributions = await response.json();
        distributionSelect.replaceChildren(new Option('Pilih distribusi', ''));
        for (const distribution of distributions) {
            distributionSelect.add(new Option(
                distribution.label,
                distribution.id,
                false,
                String(distribution.id) === String(selectedId),
            ));
        }

        distributionStatus.textContent = distributions.length
            ? `${distributions.length} distribusi tersedia.`
            : 'Belum ada distribusi untuk sekolah ini.';
    } catch {
        distributionSelect.replaceChildren(new Option('Distribusi tidak dapat dimuat', ''));
        distributionStatus.textContent = 'Distribusi gagal dimuat. Coba pilih sekolah lagi.';
    }
}

schoolSelect.addEventListener('change', () => loadDistributions());
if (schoolSelect.value) {
    loadDistributions(distributionSelect.dataset.oldDistribution);
}
