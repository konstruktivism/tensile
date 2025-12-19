<x-filament-panels::page>
    @php
        $stats = $this->getYearStats();
        $topProjects = $this->getTopProjects(5);
        $topOrganisations = $this->getTopOrganisations(5);
        $busiestMonth = $this->getBusiestMonth();
        $busiestWeek = $this->getBusiestWeek();
        $breakdown = $this->getServiceBreakdown();
        $monthlyTrends = $this->getMonthlyTrends();
        $marketingInsights = $this->getMarketingInsights();
        $projectDistribution = $this->getProjectDistributionData();
        $weekendStats = $this->getWeekendStats();
        $dayOfWeekStats = $this->getDayOfWeekStats();
        $workingDaysStats = $this->getWorkingDaysStats();
        $avgHoursPerWeek = $this->getAverageHoursPerWeek();
        $revenueEfficiency = $this->getRevenueEfficiency();
        $growthTrends = $this->getGrowthTrends();
        $quarterlyTrends = $this->getQuarterlyTrends();
        $projectProfitability = $this->getProjectProfitability();
        $clientValue = $this->getClientValueAnalysis();
        $serviceEfficiency = $this->getServiceEfficiency();
        $fixedVsHourly = $this->getFixedVsHourlyComparison();
        $taskPatterns = $this->getTaskCompletionPatterns();
        $businessInsights = $this->getBusinessInsights();
    @endphp

    <div class="space-y-8">
        <div class="flex justify-end">
            <div>
                <label class="block text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">Year</label>
                <select 
                    wire:model.live="selectedYear" 
                    class="fi-input block w-32 rounded-lg border-none bg-white px-3 py-1.5 text-base text-gray-900 outline-none transition duration-75 focus:ring-2 focus:ring-yellow-400 dark:bg-gray-800 dark:text-white"
                >
                    @foreach($this->getYearOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold tracking-tight mb-2 text-gray-900 dark:text-white">Deconstructed Year</h1>
            <p class="text-gray-700 dark:text-gray-300 font-medium">{{ $this->selectedYear }} in review</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-lg font-medium text-gray-900 dark:text-white mb-2">Total Hours</div>
                <div class="text-4xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_hours'], 0) }}</div>
                <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">hours worked</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-lg font-medium text-gray-900 dark:text-white mb-2">Total Tasks</div>
                <div class="text-4xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_tasks'], 0) }}</div>
                <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">tasks completed</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-lg font-medium text-gray-900 dark:text-white mb-2">Total Revenue</div>
                <div class="text-4xl font-bold text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::formatCurrency($stats['revenue'], '€', 0) }}</div>
                <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">revenue generated</div>
            </div>
        </div>

        @if(!empty($marketingInsights))
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <h2 class="text-2xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Numbers</h2>
            <div class="space-y-3">
                @foreach($marketingInsights as $insight)
                <div class="flex items-center gap-3 text-base text-gray-900 dark:text-gray-100">
                    <span class="text-yellow-400 font-medium">•</span>
                    <span class="font-medium">{{ $insight }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Monthly Hours Trend</h2>
                <div wire:ignore>
                    <canvas id="monthlyHoursChart" height="300"></canvas>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Monthly Revenue Trend</h2>
                <div wire:ignore>
                    <canvas id="monthlyRevenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <h2 class="text-xl font-medium mb-4 text-gray-900 dark:text-white">Quarterly Trends</h2>
            <div wire:ignore>
                <canvas id="quarterlyChart" height="300"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Top Projects</h2>
                <div class="space-y-3">
                    @foreach($topProjects as $index => $project)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-1">
                            <div class="text-lg font-medium text-gray-900 dark:text-white">{{ $project['name'] }}</div>
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $project['organisation'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($project['hours'], 1) }}h</div>
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $project['percentage'] }}%</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Top Organisations</h2>
                <div class="space-y-3">
                    @foreach($topOrganisations as $index => $org)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-1">
                            <div class="text-lg font-medium text-gray-900 dark:text-white">{{ $org['name'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($org['hours'], 1) }}h</div>
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $org['percentage'] }}%</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Work Breakdown</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $breakdown['service'] }}%</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">Service</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $breakdown['billable'] }}%</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">Billable</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $breakdown['internal'] }}%</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">Internal</div>
                </div>
            </div>
        </div>

        @if($avgHoursPerWeek > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                <div class="text-lg font-medium text-gray-900 dark:text-white mb-1">Average Hours Per Week</div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($avgHoursPerWeek, 1) }}h</div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Weekend vs Weekday</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <div class="text-lg font-medium text-gray-900 dark:text-white">Weekdays</div>
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $weekendStats['weekday_days'] }} days worked</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($weekendStats['weekday_hours'], 1) }}h</div>
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $weekendStats['weekday_percent'] }}%</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <div class="text-lg font-medium text-gray-900 dark:text-white">Weekends</div>
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $weekendStats['weekend_days'] }} days worked</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($weekendStats['weekend_hours'], 1) }}h</div>
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $weekendStats['weekend_percent'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Most Productive Days</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
                @if(!empty($dayOfWeekStats))
                    @foreach(array_slice($dayOfWeekStats, 0, 5) as $dayStat)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-1">
                            <div class="text-lg font-medium text-gray-900 dark:text-white">{{ $dayStat['day'] }}</div>
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $dayStat['days_worked'] }} days</div>
                        </div>
                        <div class="text-right">
                            <div class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($dayStat['hours'], 1) }}h</div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center text-base text-gray-700 dark:text-gray-300 py-4 font-medium">No data available</div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Working Days Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $workingDaysStats['days_worked'] }}</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">Days Worked</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $workingDaysStats['days_off'] }}</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">Days Off</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $workingDaysStats['work_percent'] }}%</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">Work Rate</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($workingDaysStats['avg_hours_per_day'], 1) }}h</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">Avg/Day</div>
                </div>
            </div>
        </div>

        @if($growthTrends['has_prev_year_data'])
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <h2 class="text-2xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Year-over-Year Growth</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">Revenue Growth</div>
                    <div class="text-4xl font-bold text-gray-900 dark:text-white">{{ $growthTrends['revenue_growth'] > 0 ? '+' : '' }}{{ number_format($growthTrends['revenue_growth'], 1) }}%</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">{{ \App\Helpers\CurrencyHelper::formatCurrency($growthTrends['current_revenue'], '€', 0) }} vs {{ \App\Helpers\CurrencyHelper::formatCurrency($growthTrends['prev_revenue'], '€', 0) }}</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">Hours Growth</div>
                    <div class="text-4xl font-bold text-gray-900 dark:text-white">{{ $growthTrends['hours_growth'] > 0 ? '+' : '' }}{{ number_format($growthTrends['hours_growth'], 1) }}%</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">{{ number_format($growthTrends['current_hours'], 0) }}h vs {{ number_format($growthTrends['prev_hours'], 0) }}h</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">Tasks Growth</div>
                    <div class="text-4xl font-bold text-gray-900 dark:text-white">{{ $growthTrends['tasks_growth'] > 0 ? '+' : '' }}{{ number_format($growthTrends['tasks_growth'], 1) }}%</div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Revenue Efficiency</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::formatCurrency($revenueEfficiency['revenue_per_billable_hour'], '€', 0) }}</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">Revenue per Billable Hour</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $revenueEfficiency['utilization_rate'] }}%</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">Utilization Rate</div>
                    <div class="text-base font-medium text-gray-600 dark:text-gray-400 mt-1">{{ number_format($revenueEfficiency['billable_hours'], 1) }}h / {{ number_format($revenueEfficiency['total_hours'], 1) }}h</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::formatCurrency($revenueEfficiency['revenue_per_total_hour'], '€', 0) }}</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">Revenue per Total Hour</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Quarterly Performance</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach($quarterlyTrends as $quarter)
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $quarter['label'] }}</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($quarter['hours'], 1) }}h</div>
                    <div class="text-base font-medium text-gray-700 dark:text-gray-300 mt-1">{{ \App\Helpers\CurrencyHelper::formatCurrency($quarter['revenue'], '€', 0) }}</div>
                    <div class="text-base font-medium text-gray-600 dark:text-gray-400 mt-1">{{ $quarter['tasks'] }} tasks</div>
                </div>
                @endforeach
            </div>
        </div>

        @if(!empty($businessInsights))
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <h2 class="text-2xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Business Insights & Recommendations</h2>
            <div class="space-y-3">
                @foreach($businessInsights as $insight)
                <div class="flex items-start gap-3 text-base">
                    <span class="text-yellow-400 font-medium mt-1">•</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $insight }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Top Clients by Value</h2>
                <div class="space-y-3 mb-4">
                    @if(!empty($clientValue['clients']))
                        @foreach(array_slice($clientValue['clients'], 0, 5) as $client)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-1">
                                <div class="text-lg font-medium text-gray-900 dark:text-white">{{ $client['name'] }}</div>
                                <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $client['project_count'] }} projects • {{ number_format($client['days_active']) }} active days</div>
                            </div>
                            <div class="text-right">
                                <div class="text-base font-bold text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::formatCurrency($client['revenue'], '€', 0) }}</div>
                                <div class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ number_format($client['total_hours'], 1) }}h</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-gray-700 dark:text-gray-300 py-4 font-medium">No data available</div>
                    @endif
                </div>
                @if($clientValue['revenue_concentration'] > 0)
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-lg font-medium text-gray-900 dark:text-white">Top 3 clients represent</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $clientValue['revenue_concentration'] }}%</div>
                    <div class="text-lg font-medium text-gray-700 dark:text-gray-300">of total revenue</div>
                </div>
                @endif
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Project Profitability</h2>
                <div class="space-y-3">
                    @if(!empty($projectProfitability['projects']))
                        @foreach(array_slice($projectProfitability['projects'], 0, 5) as $project)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-1">
                                <div class="text-lg font-medium text-gray-900 dark:text-white">{{ $project['name'] }}</div>
                                <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ number_format($project['billable_hours'], 1) }}h billable</div>
                            </div>
                            <div class="text-right">
                                <div class="text-base font-bold text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::formatCurrency($project['revenue_per_hour'], '€', 0) }}/h</div>
                                <div class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ $project['efficiency_percent'] }}% efficiency</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-gray-700 dark:text-gray-300 py-4 font-medium">No data available</div>
                    @endif
                </div>
                @if($projectProfitability['avg_hourly_rate'] > 0)
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-lg font-medium text-gray-900 dark:text-white">Average hourly rate</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ \App\Helpers\CurrencyHelper::formatCurrency($projectProfitability['avg_hourly_rate'], '€', 0) }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Service Efficiency</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <div class="text-lg font-medium text-gray-900 dark:text-white">Billable Hours</div>
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ number_format($serviceEfficiency['billable_hours'], 1) }}h</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $serviceEfficiency['efficiency_score'] }}%</div>
                            <div class="text-lg font-medium text-gray-700 dark:text-gray-300">Efficiency</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <div class="text-lg font-medium text-gray-900 dark:text-white">Service Hours</div>
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ number_format($serviceEfficiency['service_hours'], 1) }}h</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $serviceEfficiency['service_percentage'] }}%</div>
                            <div class="text-lg font-medium text-gray-700 dark:text-gray-300">of total</div>
                        </div>
                    </div>
                    @if($serviceEfficiency['service_to_billable_ratio'] > 0)
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-lg font-medium text-gray-700 dark:text-gray-300">Service to Billable Ratio</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($serviceEfficiency['service_to_billable_ratio'], 2) }}:1</div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Fixed vs Hourly Projects</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <div class="text-lg font-medium text-gray-900 dark:text-white">Fixed Price</div>
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $fixedVsHourly['fixed']['project_count'] }} projects</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($fixedVsHourly['fixed']['total_hours'], 1) }}h</div>
                            <div class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ $fixedVsHourly['fixed']['percentage'] }}%</div>
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">{{ $fixedVsHourly['fixed']['task_count'] }} tasks</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <div class="text-lg font-medium text-gray-900 dark:text-white">Hourly Rate</div>
                            <div class="text-base font-medium text-gray-700 dark:text-gray-300">{{ $fixedVsHourly['hourly']['project_count'] }} projects</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($fixedVsHourly['hourly']['total_hours'], 1) }}h</div>
                            <div class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ $fixedVsHourly['hourly']['percentage'] }}%</div>
                            <div class="text-base font-medium text-gray-600 dark:text-gray-400">{{ \App\Helpers\CurrencyHelper::formatCurrency($fixedVsHourly['hourly']['revenue'], '€', 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Task Completion Patterns</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $taskPatterns['quick_percent'] }}%</div>
                    <div class="text-lg font-medium text-gray-700 dark:text-gray-300 mt-1">Quick Tasks</div>
                    <div class="text-base font-medium text-gray-600 dark:text-gray-400 mt-1">≤ 30 min ({{ $taskPatterns['quick_tasks'] }})</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $taskPatterns['medium_percent'] }}%</div>
                    <div class="text-lg font-medium text-gray-700 dark:text-gray-300 mt-1">Medium Tasks</div>
                    <div class="text-base font-medium text-gray-600 dark:text-gray-400 mt-1">30min - 2h ({{ $taskPatterns['medium_tasks'] }})</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $taskPatterns['long_percent'] }}%</div>
                    <div class="text-lg font-medium text-gray-700 dark:text-gray-300 mt-1">Long Tasks</div>
                    <div class="text-base font-medium text-gray-600 dark:text-gray-400 mt-1">> 2h ({{ $taskPatterns['long_tasks'] }})</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($taskPatterns['avg_duration'], 1) }}h</div>
                    <div class="text-lg font-medium text-gray-700 dark:text-gray-300 mt-1">Avg Duration</div>
                    <div class="text-base font-medium text-gray-600 dark:text-gray-400 mt-1">{{ number_format($taskPatterns['min_duration'], 2) }}h - {{ number_format($taskPatterns['max_duration'], 1) }}h</div>
                </div>
            </div>
        </div>

        @if(count($projectDistribution['labels']) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-medium tracking-tight mb-4 text-gray-900 dark:text-white">Project Distribution</h2>
            <div wire:ignore>
                <canvas id="projectDistributionChart" height="300"></canvas>
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        function initCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(initCharts, 100);
                return;
            }

            const monthlyHoursData = @json(array_map(fn($m) => $m['hours'], $monthlyTrends));
            const monthlyHoursLabels = @json(array_map(fn($m) => $m['month'], $monthlyTrends));
            
            const monthlyRevenueData = @json(array_map(fn($m) => $m['revenue'], $monthlyTrends));
            const monthlyRevenueLabels = @json(array_map(fn($m) => $m['month'], $monthlyTrends));

            const quarterlyLabels = @json(array_map(fn($q) => $q['label'], $quarterlyTrends));
            const quarterlyHoursData = @json(array_map(fn($q) => $q['hours'], $quarterlyTrends));
            const quarterlyRevenueData = @json(array_map(fn($q) => $q['revenue'], $quarterlyTrends));

            const projectLabels = @json($projectDistribution['labels']);
            const projectData = @json($projectDistribution['data']);

            const hoursCtx = document.getElementById('monthlyHoursChart');
            const revenueCtx = document.getElementById('monthlyRevenueChart');
            const quarterlyCtx = document.getElementById('quarterlyChart');
            const distributionCtx = document.getElementById('projectDistributionChart');

            if (hoursCtx) {
                if (hoursCtx.chart) {
                    hoursCtx.chart.destroy();
                }
                hoursCtx.chart = new Chart(hoursCtx, {
                    type: 'bar',
                    data: {
                        labels: monthlyHoursLabels,
                        datasets: [{
                            label: 'Hours',
                            data: monthlyHoursData,
                            backgroundColor: 'rgba(250, 204, 21, 1)',
                            borderColor: 'rgba(250, 204, 21, 1)',
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000) return (value / 1000) + 'K';
                                        return value;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            if (revenueCtx) {
                if (revenueCtx.chart) {
                    revenueCtx.chart.destroy();
                }
                revenueCtx.chart = new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: monthlyRevenueLabels,
                        datasets: [{
                            label: 'Revenue',
                            data: monthlyRevenueData,
                            borderColor: 'rgba(250, 204, 21, 1)',
                            backgroundColor: 'rgba(250, 204, 21, 0.2)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000) return (value / 1000) + 'K';
                                        return value;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            if (quarterlyCtx) {
                if (quarterlyCtx.chart) {
                    quarterlyCtx.chart.destroy();
                }
                quarterlyCtx.chart = new Chart(quarterlyCtx, {
                    type: 'bar',
                    data: {
                        labels: quarterlyLabels,
                        datasets: [
                            {
                                label: 'Hours',
                                data: quarterlyHoursData,
                                backgroundColor: 'rgba(250, 204, 21, 1)',
                                borderColor: 'rgba(250, 204, 21, 1)',
                                borderWidth: 0,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Revenue (€)',
                                data: quarterlyRevenueData,
                                backgroundColor: 'rgba(156, 163, 175, 1)',
                                borderColor: 'rgba(156, 163, 175, 1)',
                                borderWidth: 0,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                position: 'left',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Hours'
                                },
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000) return (value / 1000) + 'K';
                                        return value;
                                    }
                                }
                            },
                            y1: {
                                type: 'linear',
                                position: 'right',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Revenue (€)'
                                },
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000) return (value / 1000) + 'K';
                                        return value;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            @if(count($projectDistribution['labels']) > 0)
            if (distributionCtx) {
                if (distributionCtx.chart) {
                    distributionCtx.chart.destroy();
                }
                distributionCtx.chart = new Chart(distributionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: projectLabels,
                        datasets: [{
                            data: projectData,
                            backgroundColor: [
                                'rgba(250, 204, 21, 1)',
                                'rgba(209, 213, 219, 1)',
                                'rgba(250, 204, 21, 0.7)',
                                'rgba(156, 163, 175, 1)',
                                'rgba(250, 204, 21, 0.5)',
                                'rgba(229, 231, 235, 1)',
                                'rgba(250, 204, 21, 0.3)',
                                'rgba(243, 244, 246, 1)',
                                'rgba(250, 204, 21, 0.8)',
                                'rgba(107, 114, 128, 1)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            }
            @endif
        }

        document.addEventListener('DOMContentLoaded', initCharts);
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updated', () => {
                setTimeout(initCharts, 100);
            });
        });
    </script>
</x-filament-panels::page>
