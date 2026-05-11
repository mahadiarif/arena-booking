@props([
    'name' => 'date',
    'value' => null,
    'label' => 'Select Date',
    'minDate' => null,
])

<div x-data="calendarPicker({
    selectedDate: '{{ $value ?? date('Y-m-d') }}',
    name: '{{ $name }}',
    minDate: '{{ $minDate }}'
})" class="relative">
    @if($label)
    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">{{ $label }}</label>
    @endif
    
    <div class="relative">
        <input type="hidden" :name="name" :value="selectedDate">
        <button type="button" @click="toggle()" 
                {{ $attributes->merge(['class' => 'w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-3 text-sm font-black text-slate-700 flex items-center justify-between hover:border-blue-400 transition-all shadow-sm']) }}>
            <span x-text="formatDate(selectedDate)"></span>
        </button>

        {{-- Popover --}}
        <div x-show="open" @click.away="open = false" x-cloak x-transition
             class="absolute z-[100] mt-2 p-6 bg-white border border-slate-100 rounded-[2rem] shadow-2xl shadow-slate-200/50 w-72 origin-top">
            
            {{-- Month Picker Header --}}
            <div class="flex items-center justify-between mb-6">
                <button type="button" @click="prevMonth()" class="p-2 hover:bg-slate-50 rounded-xl transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="text-sm font-black text-slate-800 tracking-tight" x-text="monthName + ' ' + year"></div>
                <button type="button" @click="nextMonth()" class="p-2 hover:bg-slate-50 rounded-xl transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            {{-- Days --}}
            <div class="grid grid-cols-7 gap-1 mb-2">
                <template x-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']">
                    <div class="text-center text-[10px] font-black text-slate-400 uppercase tracking-widest py-2" x-text="day"></div>
                </template>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-7 gap-1">
                <template x-for="blank in blankDays">
                    <div class="text-center p-2"></div>
                </template>
                <template x-for="date in noOfDays">
                    <div class="text-center">
                        <button type="button" 
                                @click="selectDate(date)"
                                :disabled="isDisabled(date)"
                                class="w-8 h-8 rounded-xl text-xs font-black transition-all flex items-center justify-center mx-auto"
                                :class="{
                                    'bg-blue-600 text-white shadow-lg shadow-blue-100': isSelected(date),
                                    'text-slate-700 hover:bg-blue-50 hover:text-blue-600': !isSelected(date) && !isDisabled(date),
                                    'text-slate-300 cursor-not-allowed': isDisabled(date),
                                    'bg-slate-50': isToday(date) && !isSelected(date)
                                }">
                            <span x-text="date"></span>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function calendarPicker(config) {
    return {
        open: false,
        name: config.name,
        selectedDate: config.selectedDate,
        minDate: config.minDate,
        year: '',
        month: '',
        monthName: '',
        noOfDays: [],
        blankDays: [],

        init() {
            let date = new Date(this.selectedDate);
            this.year = date.getFullYear();
            this.month = date.getMonth();
            this.getDays();
        },

        toggle() {
            this.open = !this.open;
        },

        formatDate(dateStr) {
            let date = new Date(dateStr);
            return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        },

        getDays() {
            let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
            let dayOfWeek = new Date(this.year, this.month).getDay();
            let blankdaysArray = [];
            for (var i = 1; i <= dayOfWeek; i++) { blankdaysArray.push(i); }
            let daysArray = [];
            for (var i = 1; i <= daysInMonth; i++) { daysArray.push(i); }
            this.blankDays = blankdaysArray;
            this.noOfDays = daysArray;
            this.monthName = new Date(this.year, this.month).toLocaleString('default', { month: 'long' });
        },

        prevMonth() {
            if (this.month == 0) {
                this.month = 11;
                this.year--;
            } else {
                this.month--;
            }
            this.getDays();
        },

        nextMonth() {
            if (this.month == 11) {
                this.month = 0;
                this.year++;
            } else {
                this.month++;
            }
            this.getDays();
        },

        selectDate(date) {
            let selected = new Date(this.year, this.month, date);
            this.selectedDate = selected.toISOString().split('T')[0];
            this.open = false;
            this.$dispatch('date-selected', this.selectedDate);
        },

        isSelected(date) {
            const d = new Date(this.year, this.month, date);
            return d.toISOString().split('T')[0] === this.selectedDate;
        },

        isToday(date) {
            const today = new Date();
            const d = new Date(this.year, this.month, date);
            return today.toDateString() === d.toDateString();
        },

        isDisabled(date) {
            if (!this.minDate) return false;
            const min = new Date(this.minDate);
            const d = new Date(this.year, this.month, date);
            return d < min.setHours(0,0,0,0);
        }
    }
}
</script>
