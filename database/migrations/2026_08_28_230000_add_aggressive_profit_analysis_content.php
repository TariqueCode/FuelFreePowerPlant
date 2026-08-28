<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $content = <<<'HTML'
<h1>Profit Analysis</h1>
<p>This section presents the financial analysis framework for the proposed 1 MW Flywheel-Based Clean Energy Power Plant.</p>

<section id="aggressive">
    <h2>Aggressive Scenario</h2>
    <p>The aggressive scenario is based on an electricity selling price of <strong>BDT 14/kWh</strong>, maintenance cost of <strong>BDT 1/kWh</strong>, and a net operating margin of <strong>BDT 13/kWh</strong>.</p>

    <h3>Project Assumptions</h3>
    <ul>
        <li>Plant capacity: <strong>1 MW</strong></li>
        <li>Installed capacity: <strong>1,000 kW</strong></li>
        <li>Load factor: <strong>50%</strong></li>
        <li>Operation: <strong>24 hours/day</strong></li>
        <li>Operating days: <strong>365 days/year</strong></li>
        <li>Electricity selling price: <strong>BDT 14/kWh</strong></li>
        <li>Maintenance cost: <strong>BDT 1/kWh</strong></li>
        <li>Net operating margin: <strong>BDT 13/kWh</strong></li>
        <li>Initial capital investment: <strong>BDT 26.40 Crore</strong></li>
        <li>Analysis period: <strong>5 years</strong></li>
        <li>Fuel cost: <strong>Nil</strong></li>
    </ul>

    <h3>Annual Financial Performance</h3>
    <table>
        <thead><tr><th>Particular</th><th>Amount</th></tr></thead>
        <tbody>
            <tr><td>Annual generation</td><td>4.38 million kWh</td></tr>
            <tr><td>Annual revenue</td><td>BDT 6.132 Crore</td></tr>
            <tr><td>Annual maintenance</td><td>BDT 0.438 Crore</td></tr>
            <tr><td>Annual net operating cash flow</td><td>BDT 5.694 Crore</td></tr>
        </tbody>
    </table>

    <h3>Five-Year Results</h3>
    <table>
        <thead><tr><th>Indicator</th><th>Five-Year Result</th></tr></thead>
        <tbody>
            <tr><td>Total generation</td><td>21.90 million kWh</td></tr>
            <tr><td>Total revenue</td><td>BDT 30.66 Crore</td></tr>
            <tr><td>Total maintenance</td><td>BDT 2.19 Crore</td></tr>
            <tr><td>Cumulative net operating cash flow</td><td>BDT 28.47 Crore</td></tr>
            <tr><td>Initial capital investment</td><td>BDT 26.40 Crore</td></tr>
            <tr><td>Five-year operating ROI</td><td>107.84%</td></tr>
            <tr><td>Simple payback period</td><td>Approximately 4.64 years</td></tr>
        </tbody>
    </table>

    <h3>Five-Year ROI</h3>
    <p><strong>ROI = (Cumulative Net Operating Cash Flow ÷ Initial Investment) × 100</strong></p>
    <p>= (28.47 ÷ 26.40) × 100 = <strong>107.84%</strong></p>

    <h3>Financial Interpretation</h3>
    <p>Under the stated assumptions, the model projects cumulative net operating cash flow of approximately <strong>BDT 28.47 Crore</strong> over five years against an initial investment of <strong>BDT 26.40 Crore</strong>, with an estimated simple payback period of approximately <strong>4.64 years</strong>.</p>

    <div class="notice">
        <strong>Important Financial Note</strong>
        <p>These figures are project-model assumptions and are not guaranteed returns. Actual commercial performance will depend on technical validation, measured efficiency, actual capital expenditure, operating costs, availability, charging/input-energy requirements, financing, taxes and applicable regulatory conditions.</p>
    </div>
</section>

<section id="normal">
    <h2>Normal</h2>
    <p>Normal scenario content will be added in the next verified step.</p>
</section>

<section id="conservative">
    <h2>Conservative</h2>
    <p>Conservative scenario content will be added in the next verified step.</p>
</section>

<section id="five-year-cash-flow">
    <h2>5-Year Cash Flow</h2>
    <p>Five-year cash-flow comparison will be added after the three scenarios are verified.</p>
</section>

<section id="cumulative-operating-cash-flow">
    <h2>Cumulative Operating Cash Flow</h2>
    <p>Cumulative operating cash-flow presentation will be added after the three scenarios are verified.</p>
</section>

<section id="scenario-comparison">
    <h2>Scenario Comparison</h2>
    <p>Scenario comparison will be added after the three scenario datasets are verified.</p>
</section>
HTML;

        DB::table('site_content_items')
            ->where('type', 'company')
            ->where('slug', 'profit-analysis')
            ->update([
                'content' => $content,
                'status' => 'draft',
                'published_at' => null,
                'show_in_navigation' => false,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('site_content_items')
            ->where('type', 'company')
            ->where('slug', 'profit-analysis')
            ->update([
                'content' => '<h1>Profit Analysis</h1><p>This section presents the financial analysis framework for the 1 MW Flywheel-Based Clean Energy Power Plant.</p>',
                'status' => 'draft',
                'published_at' => null,
                'show_in_navigation' => false,
                'updated_at' => now(),
            ]);
    }
};
