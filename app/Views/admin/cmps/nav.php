<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <?php
        $request = service('request');
        ?>
        <li class="nav-item">
            <a href="/" class="nav-link <?= !$request->uri->getSegment(1) ? 'active' : null; ?>">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/clients" class="nav-link <?= $request->uri->getSegment(1) == 'clients' ? 'active' : null; ?>">
                <i class="nav-icon fas fa-users"></i>
                <p>Clients</p>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="/drug_test" class="nav-link <?= $request->uri->getSegment(1) == 'drug_test' ? 'active' : null; ?>">
                <i class="nav-icon fas fa-hospital"></i>
                <p>Drug Test</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/belongings" class="nav-link <?= $request->uri->getSegment(1) == 'belongings' ? 'active' : null; ?>">
                <i class="nav-icon fas fa-suitcase"></i>
                <p>Belongings</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/case_notes" class="nav-link <?= $request->uri->getSegment(1) == 'case_notes' ? 'active' : null; ?>">
                <i class="nav-icon fas fa-folder"></i>
                <p>Case Notes</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/treatment_plan" class="nav-link <?= $request->uri->getSegment(1) == 'treatment_plan' ? 'active' : null; ?>">
                <i class="nav-icon fas fa-file"></i>
                <p>Treatment Plan</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/medication" class="nav-link <?= $request->uri->getSegment(1) == 'medication' ? 'active' : null; ?>">
                <i class="nav-icon fas fa-prescription"></i>
                <p>Medication</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="/expense" class="nav-link <?= $request->uri->getSegment(1) == 'expense' ? 'active' : null; ?>">
                <i class="nav-icon fas fa-credit-card"></i>
                <p>Expenses</p>
            </a>
        </li>
    </ul>
</nav>