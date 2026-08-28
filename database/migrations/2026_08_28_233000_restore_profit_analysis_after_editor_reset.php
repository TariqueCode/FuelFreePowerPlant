<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $item = DB::table('site_content_items')
            ->where('type', 'company')
            ->where('slug', 'profit-analysis')
            ->first(['id']);

        if (! $item) {
            return;
        }

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
<table><thead><tr><th>Particular</th><th>Amount</th></tr></thead><tbody>
<tr><td>Annual generation</td><td>4.38 million kWh</td></tr>
<tr><td>Annual revenue</td><td>BDT 6.132 Crore</td></tr>
<tr><td>Annual maintenance</td><td>BDT 0.438 Crore</td></tr>
<tr><td>Annual net operating cash flow</td><td>BDT 5.694 Crore</td></tr>
</tbody></table>
<h3>Five-Year Results</h3>
<table><thead><tr><th>Indicator</th><th>Five-Year Result</th></tr></thead><tbody>
<tr><td>Total generation</td><td>21.90 million kWh</td></tr>
<tr><td>Total revenue</td><td>BDT 30.66 Crore</td></tr>
<tr><td>Total maintenance</td><td>BDT 2.19 Crore</td></tr>
<tr><td>Cumulative net operating cash flow</td><td>BDT 28.47 Crore</td></tr>
<tr><td>Initial capital investment</td><td>BDT 26.40 Crore</td></tr>
<tr><td>Five-year operating ROI</td><td>107.84%</td></tr>
<tr><td>Simple payback period</td><td>Approximately 4.64 years</td></tr>
</tbody></table>
<h3>Five-Year ROI</h3>
<p><strong>ROI = (Cumulative Net Operating Cash Flow ÷ Initial Investment) × 100</strong></p>
<p>= (28.47 ÷ 26.40) × 100 = <strong>107.84%</strong></p>
<h3>Financial Interpretation</h3>
<p>Under the stated assumptions, the model projects cumulative net operating cash flow of approximately <strong>BDT 28.47 Crore</strong> over five years against an initial investment of <strong>BDT 26.40 Crore</strong>, with an estimated simple payback period of approximately <strong>4.64 years</strong>.</p>
<div class="notice"><strong>Important Financial Note</strong><p>These figures are project-model assumptions and are not guaranteed returns. Actual commercial performance will depend on technical validation, measured efficiency, actual capital expenditure, operating costs, availability, charging/input-energy requirements, financing, taxes and applicable regulatory conditions.</p></div>
</section>

<section id="normal">
<h2>Normal Scenario</h2>
<p>The normal scenario assumes an electricity selling price of <strong>BDT 12/kWh</strong>, maintenance cost of <strong>BDT 2/kWh</strong>, and a net operating margin of <strong>BDT 10/kWh</strong>.</p>
<h3>Project Assumptions</h3>
<ul>
<li>Plant capacity: <strong>1 MW</strong></li>
<li>Installed capacity: <strong>1,000 kW</strong></li>
<li>Load factor: <strong>50%</strong></li>
<li>Operation: <strong>24 hours/day</strong></li>
<li>Operating days: <strong>365 days/year</strong></li>
<li>Electricity selling price: <strong>BDT 12/kWh</strong></li>
<li>Maintenance cost: <strong>BDT 2/kWh</strong></li>
<li>Net operating margin: <strong>BDT 10/kWh</strong></li>
<li>Initial capital investment: <strong>BDT 26.40 Crore</strong></li>
<li>Analysis period: <strong>5 years</strong></li>
<li>Fuel cost: <strong>Nil</strong></li>
</ul>
<h3>Annual Financial Performance</h3>
<table><thead><tr><th>Particular</th><th>Amount</th></tr></thead><tbody>
<tr><td>Annual generation</td><td>4.38 million kWh</td></tr>
<tr><td>Annual revenue</td><td>BDT 5.256 Crore</td></tr>
<tr><td>Annual maintenance</td><td>BDT 0.876 Crore</td></tr>
<tr><td>Annual net operating cash flow</td><td>BDT 4.380 Crore</td></tr>
</tbody></table>
<h3>Five-Year Results</h3>
<table><thead><tr><th>Indicator</th><th>Five-Year Result</th></tr></thead><tbody>
<tr><td>Total generation</td><td>21.90 million kWh</td></tr>
<tr><td>Total revenue</td><td>BDT 26.28 Crore</td></tr>
<tr><td>Total maintenance</td><td>BDT 4.38 Crore</td></tr>
<tr><td>Cumulative operating profit</td><td>BDT 21.90 Crore</td></tr>
<tr><td>Initial capital investment</td><td>BDT 26.40 Crore</td></tr>
<tr><td>Five-year operating ROI</td><td>82.95%</td></tr>
<tr><td>Simple payback period</td><td>Approximately 6.03 years</td></tr>
</tbody></table>
<h3>Five-Year ROI</h3>
<p><strong>ROI = (Cumulative Operating Profit ÷ Initial Investment) × 100</strong></p>
<p>= (21.90 ÷ 26.40) × 100 = <strong>82.95%</strong></p>
<h3>Financial Interpretation</h3>
<p>Under the stated assumptions, the model projects cumulative operating profit of approximately <strong>BDT 21.90 Crore</strong> over five years against an initial investment of <strong>BDT 26.40 Crore</strong>. The estimated simple payback period is approximately <strong>6.03 years</strong>, so full capital recovery is not achieved within the first five years under this simplified operating model.</p>
<div class="notice"><strong>Important Financial Note</strong><p>These figures are project-model assumptions and are not guaranteed returns. The simplified operating-profit model excludes financing costs, corporate income tax, depreciation, administrative expenses not already included in maintenance, major overhaul expenditure and other extraordinary costs. Actual commercial performance will depend on technical validation, measured efficiency, actual capital expenditure, operating costs, availability, charging/input-energy requirements, financing, taxes and applicable regulatory conditions.</p></div>
</section>

<section id="conservative">
<h2>Conservative Scenario</h2>
<p>The conservative scenario assumes an electricity selling price of <strong>BDT 11/kWh</strong>, maintenance cost of <strong>BDT 4/kWh</strong>, and a net operating margin of <strong>BDT 7/kWh</strong>.</p>
<h3>Project Assumptions</h3>
<ul>
<li>Plant capacity: <strong>1 MW</strong></li>
<li>Installed capacity: <strong>1,000 kW</strong></li>
<li>Load factor: <strong>50%</strong></li>
<li>Effective average capacity: <strong>500 kW</strong></li>
<li>Operation: <strong>24 hours/day</strong></li>
<li>Operating days: <strong>365 days/year</strong></li>
<li>Electricity selling price: <strong>BDT 11/kWh</strong></li>
<li>Maintenance cost: <strong>BDT 4/kWh</strong></li>
<li>Net operating margin: <strong>BDT 7/kWh</strong></li>
<li>Initial capital investment: <strong>BDT 26.40 Crore</strong></li>
<li>Analysis period: <strong>5 years</strong></li>
</ul>
<h3>Annual Financial Performance</h3>
<table><thead><tr><th>Particular</th><th>Amount</th></tr></thead><tbody>
<tr><td>Annual generation</td><td>4.38 million kWh</td></tr>
<tr><td>Annual revenue</td><td>BDT 4.818 Crore</td></tr>
<tr><td>Annual maintenance</td><td>BDT 1.752 Crore</td></tr>
<tr><td>Annual net operating cash flow</td><td>BDT 3.066 Crore</td></tr>
</tbody></table>
<h3>Five-Year Results</h3>
<table><thead><tr><th>Indicator</th><th>Five-Year Result</th></tr></thead><tbody>
<tr><td>Total generation</td><td>21.90 million kWh</td></tr>
<tr><td>Total revenue</td><td>BDT 24.09 Crore</td></tr>
<tr><td>Total maintenance</td><td>BDT 8.76 Crore</td></tr>
<tr><td>Cumulative net operating cash flow</td><td>BDT 15.33 Crore</td></tr>
<tr><td>Initial capital investment</td><td>BDT 26.40 Crore</td></tr>
<tr><td>Five-year operating ROI</td><td>58.07%</td></tr>
<tr><td>Simple payback period</td><td>Approximately 8.61 years</td></tr>
</tbody></table>
<h3>Financial Interpretation</h3>
<p>Under the stated assumptions, the model projects cumulative net operating cash flow of approximately <strong>BDT 15.33 Crore</strong> over five years against an initial investment of <strong>BDT 26.40 Crore</strong>. At the end of Year 5, approximately <strong>BDT 11.07 Crore</strong> of the initial investment remains unrecovered, with an estimated simple payback period of approximately <strong>8.61 years</strong>.</p>
<div class="notice"><strong>Important Financial Note</strong><p>These figures are project-model assumptions and are not guaranteed returns. Actual commercial performance will depend on technical validation, measured efficiency, actual capital expenditure, operating costs, availability, charging/input-energy requirements, financing, taxes and applicable regulatory conditions.</p></div>
</section>

<section id="five-year-cash-flow">
<h2>5-Year Cash Flow</h2>
<h3>Aggressive Scenario</h3>
<table><thead><tr><th>Period</th><th>Annual Net Cash Flow</th><th>Cumulative Net Cash Flow</th><th>Capital Position</th></tr></thead><tbody>
<tr><td>Initial Investment</td><td>—</td><td>—</td><td>BDT 26.40 Cr</td></tr>
<tr><td>Year 1</td><td>BDT 5.694 Cr</td><td>BDT 5.694 Cr</td><td>BDT 20.706 Cr unrecovered</td></tr>
<tr><td>Year 2</td><td>BDT 5.694 Cr</td><td>BDT 11.388 Cr</td><td>BDT 15.012 Cr unrecovered</td></tr>
<tr><td>Year 3</td><td>BDT 5.694 Cr</td><td>BDT 17.082 Cr</td><td>BDT 9.318 Cr unrecovered</td></tr>
<tr><td>Year 4</td><td>BDT 5.694 Cr</td><td>BDT 22.776 Cr</td><td>BDT 3.624 Cr unrecovered</td></tr>
<tr><td>Year 5</td><td>BDT 5.694 Cr</td><td>BDT 28.470 Cr</td><td>BDT 2.070 Cr surplus after capital recovery</td></tr>
</tbody></table>
<h3>Normal Scenario</h3>
<table><thead><tr><th>Period</th><th>Annual Net Cash Flow</th><th>Cumulative Net Cash Flow</th><th>Unrecovered Capital</th></tr></thead><tbody>
<tr><td>Initial Investment</td><td>—</td><td>—</td><td>BDT 26.40 Cr</td></tr>
<tr><td>Year 1</td><td>BDT 4.380 Cr</td><td>BDT 4.380 Cr</td><td>BDT 22.020 Cr</td></tr>
<tr><td>Year 2</td><td>BDT 4.380 Cr</td><td>BDT 8.760 Cr</td><td>BDT 17.640 Cr</td></tr>
<tr><td>Year 3</td><td>BDT 4.380 Cr</td><td>BDT 13.140 Cr</td><td>BDT 13.260 Cr</td></tr>
<tr><td>Year 4</td><td>BDT 4.380 Cr</td><td>BDT 17.520 Cr</td><td>BDT 8.880 Cr</td></tr>
<tr><td>Year 5</td><td>BDT 4.380 Cr</td><td>BDT 21.900 Cr</td><td>BDT 4.500 Cr</td></tr>
</tbody></table>
<h3>Conservative Scenario</h3>
<table><thead><tr><th>Period</th><th>Annual Net Cash Flow</th><th>Cumulative Net Cash Flow</th><th>Unrecovered Capital</th></tr></thead><tbody>
<tr><td>Initial Investment</td><td>—</td><td>—</td><td>BDT 26.40 Cr</td></tr>
<tr><td>Year 1</td><td>BDT 3.066 Cr</td><td>BDT 3.066 Cr</td><td>BDT 23.334 Cr</td></tr>
<tr><td>Year 2</td><td>BDT 3.066 Cr</td><td>BDT 6.132 Cr</td><td>BDT 20.268 Cr</td></tr>
<tr><td>Year 3</td><td>BDT 3.066 Cr</td><td>BDT 9.198 Cr</td><td>BDT 17.202 Cr</td></tr>
<tr><td>Year 4</td><td>BDT 3.066 Cr</td><td>BDT 12.264 Cr</td><td>BDT 14.136 Cr</td></tr>
<tr><td>Year 5</td><td>BDT 3.066 Cr</td><td>BDT 15.330 Cr</td><td>BDT 11.070 Cr</td></tr>
</tbody></table>
</section>

<section id="cumulative-operating-cash-flow">
<h2>Cumulative Operating Cash Flow</h2>
<table><thead><tr><th>Year</th><th>Aggressive</th><th>Normal</th><th>Conservative</th></tr></thead><tbody>
<tr><td>Year 1</td><td>BDT 5.694 Cr</td><td>BDT 4.380 Cr</td><td>BDT 3.066 Cr</td></tr>
<tr><td>Year 2</td><td>BDT 11.388 Cr</td><td>BDT 8.760 Cr</td><td>BDT 6.132 Cr</td></tr>
<tr><td>Year 3</td><td>BDT 17.082 Cr</td><td>BDT 13.140 Cr</td><td>BDT 9.198 Cr</td></tr>
<tr><td>Year 4</td><td>BDT 22.776 Cr</td><td>BDT 17.520 Cr</td><td>BDT 12.264 Cr</td></tr>
<tr><td>Year 5</td><td>BDT 28.470 Cr</td><td>BDT 21.900 Cr</td><td>BDT 15.330 Cr</td></tr>
</tbody></table>
</section>

<section id="scenario-comparison">
<h2>Scenario Comparison</h2>
<table><thead><tr><th>Metric</th><th>Aggressive</th><th>Normal</th><th>Conservative</th></tr></thead><tbody>
<tr><td>Selling price</td><td>BDT 14/kWh</td><td>BDT 12/kWh</td><td>BDT 11/kWh</td></tr>
<tr><td>Maintenance</td><td>BDT 1/kWh</td><td>BDT 2/kWh</td><td>BDT 4/kWh</td></tr>
<tr><td>Net operating margin</td><td>BDT 13/kWh</td><td>BDT 10/kWh</td><td>BDT 7/kWh</td></tr>
<tr><td>Annual generation</td><td>4.38 million kWh</td><td>4.38 million kWh</td><td>4.38 million kWh</td></tr>
<tr><td>Annual revenue</td><td>BDT 6.132 Cr</td><td>BDT 5.256 Cr</td><td>BDT 4.818 Cr</td></tr>
<tr><td>Annual maintenance</td><td>BDT 0.438 Cr</td><td>BDT 0.876 Cr</td><td>BDT 1.752 Cr</td></tr>
<tr><td>Annual net operating cash flow</td><td>BDT 5.694 Cr</td><td>BDT 4.380 Cr</td><td>BDT 3.066 Cr</td></tr>
<tr><td>5-year cumulative operating cash flow</td><td>BDT 28.47 Cr</td><td>BDT 21.90 Cr</td><td>BDT 15.33 Cr</td></tr>
<tr><td>Operating ROI</td><td>107.84%</td><td>82.95%</td><td>58.07%</td></tr>
<tr><td>Simple payback</td><td>4.64 years</td><td>6.03 years</td><td>8.61 years</td></tr>
</tbody></table>
<p><strong>Source note:</strong> These figures are project-model assumptions for illustrative analysis and are not guaranteed returns.</p>
</section>
HTML;

        DB::table('site_content_items')
            ->where('id', $item->id)
            ->update([
                'title' => 'Profit Analysis',
                'excerpt' => 'Financial analysis and scenario comparison for the 1 MW Flywheel-Based Clean Energy Power Plant.',
                'content' => $content,
                'status' => 'draft',
                'published_at' => null,
                'show_in_navigation' => false,
                'updated_at' => now(),
            ]);
    }

    public function down(): void {}
};
