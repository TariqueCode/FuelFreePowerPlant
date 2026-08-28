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
            ->first();

        if (! $item) {
            return;
        }

        $content = (string) $item->content;

        $placeholder = '<h2 id="normal">Normal</h2>\n<p>Normal scenario content will be added in the next verified step.</p>';

        $normal = <<<'HTML'
<h2 id="normal">Normal Scenario</h2>
<p>The normal scenario is based on an electricity selling price of <strong>BDT 12/kWh</strong>, maintenance cost of <strong>BDT 2/kWh</strong>, and a net operating margin of <strong>BDT 10/kWh</strong>.</p>

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
<table>
<thead><tr><th>Particular</th><th>Amount</th></tr></thead>
<tbody>
<tr><td>Annual generation</td><td>4.38 million kWh</td></tr>
<tr><td>Annual revenue</td><td>BDT 5.256 Crore</td></tr>
<tr><td>Annual maintenance</td><td>BDT 0.876 Crore</td></tr>
<tr><td>Annual net operating cash flow</td><td>BDT 4.380 Crore</td></tr>
</tbody>
</table>

<h3>Five-Year Results</h3>
<table>
<thead><tr><th>Indicator</th><th>Five-Year Result</th></tr></thead>
<tbody>
<tr><td>Total generation</td><td>21.90 million kWh</td></tr>
<tr><td>Total revenue</td><td>BDT 26.28 Crore</td></tr>
<tr><td>Total maintenance</td><td>BDT 4.38 Crore</td></tr>
<tr><td>Cumulative operating profit</td><td>BDT 21.90 Crore</td></tr>
<tr><td>Initial capital investment</td><td>BDT 26.40 Crore</td></tr>
<tr><td>Five-year operating ROI</td><td>82.95%</td></tr>
<tr><td>Simple payback period</td><td>Approximately 6.03 years</td></tr>
</tbody>
</table>

<h3>Five-Year ROI</h3>
<p><strong>ROI = (Cumulative Operating Profit ÷ Initial Investment) × 100</strong></p>
<p>= (21.90 ÷ 26.40) × 100 = <strong>82.95%</strong></p>

<h3>Financial Interpretation</h3>
<p>Under the stated assumptions, the model projects cumulative operating profit of approximately <strong>BDT 21.90 Crore</strong> over five years against an initial investment of <strong>BDT 26.40 Crore</strong>. The estimated simple payback period is approximately <strong>6.03 years</strong>, so full capital recovery is not achieved within the first five years under this simplified operating model.</p>

<div class="notice">
<strong>Important Financial Note</strong>
<p>The BDT 21.90 Crore figure represents five-year cumulative operating profit in this simplified project model. It should not be presented as accounting net profit unless financing costs, taxes, depreciation, administrative expenses and other excluded costs are separately modeled. Actual commercial performance will depend on technical validation, measured efficiency, actual capital expenditure, operating costs, availability, charging/input-energy requirements, financing, taxes and applicable regulatory conditions.</p>
</div>
HTML;

        if (str_contains($content, $placeholder)) {
            $content = str_replace($placeholder, $normal, $content, $count);
            DB::table('site_content_items')
                ->where('id', $item->id)
                ->update([
                    'content' => $content,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void {}
};
