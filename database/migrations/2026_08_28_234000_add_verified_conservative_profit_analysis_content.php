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
            ->first(['id', 'content']);

        if (!$item || !$item->content) {
            return;
        }

        $content = (string) $item->content;

        $placeholderVariants = [
            '<h2 id="conservative">Conservative</h2>\n<p>Conservative scenario content will be added in the next verified step.</p>',
            '<h2 id="conservative">Conservative</h2>\r\n<p>Conservative scenario content will be added in the next verified step.</p>',
        ];

        $replacement = <<<'HTML'
<section id="conservative">
<h2>Conservative Scenario</h2>
<p>The conservative scenario assumes an electricity selling price of <strong>BDT 11/kWh</strong>, maintenance cost of <strong>BDT 4/kWh</strong>, and a net operating margin of <strong>BDT 7/kWh</strong>.</p>

<h3>Project Assumptions</h3>
<ul>
<li>Plant capacity: <strong>1 MW</strong></li>
<li>Installed capacity: <strong>1,000 kW</strong></li>
<li>Load factor: <strong>50%</strong></li>
<li>Operation: <strong>24 hours/day</strong></li>
<li>Operating days: <strong>365 days/year</strong></li>
<li>Electricity selling price: <strong>BDT 11/kWh</strong></li>
<li>Maintenance cost: <strong>BDT 4/kWh</strong></li>
<li>Net operating margin: <strong>BDT 7/kWh</strong></li>
<li>Initial capital investment: <strong>BDT 26.40 Crore</strong></li>
<li>Analysis period: <strong>5 years</strong></li>
</ul>

<h3>Annual Financial Performance</h3>
<table>
<thead><tr><th>Particular</th><th>Amount</th></tr></thead>
<tbody>
<tr><td>Annual generation</td><td>4.38 million kWh</td></tr>
<tr><td>Annual revenue</td><td>BDT 4.818 Crore</td></tr>
<tr><td>Annual maintenance</td><td>BDT 1.752 Crore</td></tr>
<tr><td>Annual net operating cash flow</td><td>BDT 3.066 Crore</td></tr>
</tbody>
</table>

<h3>Five-Year Results</h3>
<table>
<thead><tr><th>Indicator</th><th>Five-Year Result</th></tr></thead>
<tbody>
<tr><td>Total generation</td><td>21.90 million kWh</td></tr>
<tr><td>Total revenue</td><td>BDT 24.09 Crore</td></tr>
<tr><td>Total maintenance</td><td>BDT 8.76 Crore</td></tr>
<tr><td>Cumulative net operating cash flow</td><td>BDT 15.33 Crore</td></tr>
<tr><td>Initial capital investment</td><td>BDT 26.40 Crore</td></tr>
<tr><td>Five-year operating ROI</td><td>58.07%</td></tr>
<tr><td>Simple payback period</td><td>Approximately 8.61 years</td></tr>
</tbody>
</table>

<h3>Five-Year ROI</h3>
<p><strong>ROI = (Cumulative Net Operating Cash Flow ÷ Initial Investment) × 100</strong></p>
<p>= (15.33 ÷ 26.40) × 100 = <strong>58.07%</strong></p>

<h3>Financial Interpretation</h3>
<p>Under the stated assumptions, the model projects cumulative net operating cash flow of approximately <strong>BDT 15.33 Crore</strong> over five years against an initial investment of <strong>BDT 26.40 Crore</strong>. At the end of Year 5, approximately <strong>BDT 11.07 Crore</strong> of the initial investment remains unrecovered, with an estimated simple payback period of approximately <strong>8.61 years</strong>.</p>

<div class="notice">
<strong>Important Financial Note</strong>
<p>These figures are project-model assumptions and are not guaranteed returns. Actual commercial performance will depend on technical validation, measured efficiency, actual capital expenditure, operating costs, availability, charging/input-energy requirements, financing, taxes and applicable regulatory conditions.</p>
</div>
</section>
HTML;

        foreach ($placeholderVariants as $placeholder) {
            if (str_contains($content, $placeholder)) {
                $content = str_replace($placeholder, $replacement, $content, $count);
                if ($count > 0) {
                    DB::table('site_content_items')
                        ->where('id', $item->id)
                        ->update([
                            'content' => $content,
                            'updated_at' => now(),
                        ]);
                }
                return;
            }
        }
    }

    public function down(): void
    {
        // Intentionally left empty so the verified content is not automatically removed.
    }
};
