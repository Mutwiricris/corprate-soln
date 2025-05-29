<x-layouts.app :title="__('Dashboard')">

    <div>

        <main class="container p-5">
            <h1 class="font-extrabold text-3xl py-2 dark:text-white ">Dashboard</h1>

            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2">
                        <flux:select size="sm" class="">
                            <option>Last 7 days</option>
                            <option>Last 14 days</option>
                            <option selected>Last 30 days</option>
                            <option>Last 60 days</option>
                            <option>Last 90 days</option>
                        </flux:select>

                        <flux:subheading class="max-md:hidden whitespace-nowrap">compared to</flux:subheading>

                        <flux:select size="sm" class="max-md:hidden">
                            <option selected>Previous period</option>
                            <option>Same period last year</option>
                            <option>Last month</option>
                            <option>Last quarter</option>
                            <option>Last 6 months</option>
                            <option>Last 12 months</option>
                        </flux:select>
                    </div>

                    <flux:separator vertical class="max-lg:hidden mx-2 my-2" />

                    <div class="max-lg:hidden flex justify-start items-center gap-2">
                        <flux:subheading class="whitespace-nowrap">Filter by:</flux:subheading>

                        <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">Amount</flux:badge>
                        <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg" class="max-md:hidden">Status</flux:badge>
                        <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">More filters...</flux:badge>
                    </div>
                </div>

                <flux.tabs variant="segmented" class="w-auto! ml-2" size="sm">
                    <flux.tab icon="list-bullet" icon:variant="outline" />
                    <flux.tab icon="squares-2x2" icon:variant="outline" />
                </flux.tabs>
            </div>

            <div class="flex gap-6 mb-6">
                @foreach ($stats as $stat)
                    <div class="relative flex-1 border rounded-lg px-6 py-4 bg-zinc-50 dark:bg-zinc-700 {{
                    $loop->iteration > 1 ? 'max-md:hidden' : '' }}  {{ $loop->iteration > 3 ? 'max-lg:hidden' : '' }}">
                        <flux:subheading class="text-gray-900 dark:text-white font-bold">{{ $stat['title']
                        }}</flux:subheading>

                        <flux:heading size="xl" class="mb-2 font-extrabold">{{ $stat['value'] }}</flux:heading>

                        <div class="flex items-center gap-1 font-medium text-sm @if ($stat['trendUp']) text-green-600 dark:text-green-400 @else text-red-500 dark:text-red-400 @endif">
                            <flux:icon :icon="$stat['trendUp'] ? 'arrow-trending-up' : 'arrow-trending-down'" variant="micro" /> {{ $stat['trend'] }}
                        </div>

                        <div class="absolute top-0 right-0 pr-2 pt-2">
                            <flux:button icon="ellipsis-horizontal" variant="subtle" size="sm" />
                        </div>
                    </div>
                @endforeach
            </div>

            {{--            <flux:table>--}}
            {{--                <flux:table.columns>--}}
            {{--                    <flux:table.column></flux:table.column>--}}
            {{--                    <flux:table.column class="max-md:hidden">ID</flux:table.column>--}}
            {{--                    <flux:table.column class="max-md:hidden">Date</flux:table.column>--}}
            {{--                    <flux:table.column class="max-md:hidden">Status</flux:table.column>--}}
            {{--                    <flux:table.column><span class="max-md:hidden">Customer</span><div class="md:hidden w-6"></div></flux:table.column>--}}
            {{--                    <flux:table.column>Purchase</flux:table.column>--}}
            {{--                    <flux:table.column>Revenue</flux:table.column>--}}
            {{--                    <flux:table.column></flux:table.column>--}}
            {{--                </flux:table.columns>--}}

            {{--                <flux:table.rows>--}}
            {{--                    @foreach ($this->rows as $row)--}}
            {{--                        <flux:table.row>--}}
            {{--                            <flux:table.cell class="pr-2"><flux:checkbox /></flux:table.cell>--}}
            {{--                            <flux:table.cell class="max-md:hidden">#{{ $row['id'] }}</flux:table.cell>--}}
            {{--                            <flux:table.cell class="max-md:hidden">{{ $row['date'] }}</flux:table.cell>--}}
            {{--                            <flux:table.cell class="max-md:hidden"><flux:badge :color="$row['status_color']" size="sm" inset="top bottom">{{ $row['status'] }}</flux:badge></flux:table.cell>--}}
            {{--                            <flux:table.cell class="min-w-6">--}}
            {{--                                <div class="flex items-center gap-2">--}}
            {{--                                    <flux:avatar src="https://i.pravatar.cc/48?img={{ $loop->index }}" size="xs" />--}}
            {{--                                    <span class="max-md:hidden">{{ $row['customer'] }}</span>--}}
            {{--                                </div>--}}
            {{--                            </flux:table.cell>--}}
            {{--                            <flux:table.cell class="max-w-6 truncate">{{ $row['purchase'] }}</flux:table.cell>--}}
            {{--                            <flux:table.cell class="" variant="strong">{{ $row['amount'] }}</flux:table.cell>--}}
            {{--                            <flux:table.cell>--}}
            {{--                                <flux:dropdown position="bottom" align="end" offset="-15">--}}
            {{--                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>--}}

            {{--                                    <flux:menu>--}}
            {{--                                        <flux:menu.item icon="document-text">View invoice</flux:menu.item>--}}
            {{--                                        <flux:menu.item icon="receipt-refund">Refund</flux:menu.item>--}}
            {{--                                        <flux:menu.item icon="archive-box" variant="danger">Archive</flux:menu.item>--}}
            {{--                                    </flux:menu>--}}
            {{--                                </flux:dropdown>--}}
            {{--                            </flux:table.cell>--}}
            {{--                        </flux:table.row>--}}
            {{--                    @endforeach--}}
            {{--                </flux:table.rows>--}}
            {{--            </flux:table>--}}

            {{--            <flux:pagination :paginator="$this->paginator" />--}}


            <x-table  />
        </main>
    </div>
</x-layouts.app>
