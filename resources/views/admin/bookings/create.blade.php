@extends('layouts.admin')
@section('title','New Booking')
@section('breadcrumb','New Booking')
@section('content')
<form method="POST" action="{{ route('admin.bookings.store') }}"
  x-data="{
    customerId:null, customerName:'', customerPhone:'', customerCredit:0,
    customerSearch:'', customerResults:[], showDrop:false,
    venueId:'{{ $slot ? $slot->venue_id : '' }}', 
    slotId:'{{ $slot ? $slot->id : '' }}', 
    slots:[], loadingSlots:false,
    totalAmount:{{ $slot ? $slot->venue->price ?? 0 : 0 }}, 
    hourlyRate:{{ $slot ? $slot->venue->hourly_rate ?? 0 : 0 }},
    collectPayment:false, payMethod:'cash', payAmount:0,
    isRecurring:false, recurType:'weekly', endType:'on_date',
    participants:[],
    addP(){ this.participants.push({name:'',phone:'',note:''}) },
    removeP(i){ this.participants.splice(i,1) },
    async searchC(){
      if(this.customerSearch.length<2){this.customerResults=[];return;}
      const r=await fetch('/admin/customers/search?q='+encodeURIComponent(this.customerSearch));
      this.customerResults=await r.json(); this.showDrop=true;
    },
    selectC(c){
      this.customerId=c.id; this.customerName=c.name;
      this.customerPhone=c.phone; this.customerCredit=c.credit_balance;
      this.customerSearch=c.name; this.showDrop=false;
    },
    async fetchSlots(){
      if(!this.venueId || !this.$refs.dt?.value){ this.slots=[]; return; }
      this.loadingSlots=true; this.slotId=''; this.slots=[];
      try {
        const r=await fetch('/admin/slots?venue_id='+this.venueId+'&date='+this.$refs.dt.value+'&format=json');
        this.slots=await r.json();
      } catch(e){ console.error(e); }
      this.loadingSlots=false;
    },
    calcAmt(){
      const s=this.slots.find(x=>x.id==this.slotId);
      if(!s) return;
      this.totalAmount=((s.duration_minutes/60)*this.hourlyRate).toFixed(2);
      if(this.collectPayment) this.payAmount=this.totalAmount;
    }
  }"
  x-on:click.away="showDrop=false">
@csrf

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
<div class="xl:col-span-2 space-y-5">

{{-- 1: Customer --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
  <h3 class="text-sm font-semibold text-slate-700 mb-4">1 — Customer</h3>
  <div class="relative">
    <input type="text" x-model="customerSearch"
           x-on:input.debounce.300ms="searchC()"
           x-on:focus="if(customerResults.length) showDrop=true"
           placeholder="Search customer by name or phone…"
           class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
    <input type="hidden" name="customer_id" :value="customerId">
    <div x-show="showDrop && customerResults.length" x-cloak
         class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden">
      <template x-for="c in customerResults" :key="c.id">
        <div x-on:click="selectC(c)" class="px-4 py-2.5 hover:bg-blue-50 cursor-pointer border-b border-slate-50 last:border-0">
          <p class="font-semibold text-slate-800 text-sm" x-text="c.name"></p>
          <p class="text-xs text-slate-400" x-text="c.phone"></p>
        </div>
      </template>
    </div>
  </div>
  <div x-show="customerId" x-cloak
       class="mt-3 bg-blue-50 border border-blue-100 rounded-xl px-4 py-2.5 flex justify-between items-center">
    <div>
      <p class="font-semibold text-blue-800 text-sm" x-text="customerName"></p>
      <p class="text-xs text-blue-500" x-text="customerPhone"></p>
    </div>
    <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-lg">
      ৳<span x-text="parseFloat(customerCredit||0).toFixed(0)"></span> credit
    </span>
  </div>
  @error('customer_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>

{{-- 2: Venue & Slot --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
  <h3 class="text-sm font-semibold text-slate-700 mb-4">2 — Venue & Slot</h3>
  <div class="grid grid-cols-3 gap-3">
    <div>
      <label class="text-xs font-semibold text-slate-500 block mb-1.5">Venue</label>
      <select name="venue_id" x-model="venueId"
              x-on:change="hourlyRate=$event.target.options[$event.target.selectedIndex].dataset.rate||0; fetchSlots()"
              class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">— Venue —</option>
        @foreach($venues as $v)
        <option value="{{ $v->id }}" data-rate="{{ $v->hourly_rate }}">{{ $v->name }}</option>
        @endforeach
      </select>
      @error('venue_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="text-xs font-semibold text-slate-500 block mb-1.5">Date</label>
      <input type="date" x-ref="dt" x-on:change="fetchSlots()" value="{{ now()->toDateString() }}"
             class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
      <label class="text-xs font-semibold text-slate-500 block mb-1.5">Slot</label>
      <div x-show="loadingSlots" class="text-xs text-slate-400 py-2.5">Loading…</div>
      <select x-show="!loadingSlots" name="slot_id" x-model="slotId" x-on:change="calcAmt()"
              class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">— Select Slot —</option>
        @if($slot)
        <option value="{{ $slot->id }}" selected>{{ \Carbon\Carbon::createFromTimeString($slot->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::createFromTimeString($slot->end_time)->format('g:i A') }}</option>
        @endif
        <template x-for="s in slots" :key="s.id">
          <option :value="s.id" :disabled="!s.is_bookable"
                  x-text="s.label + (s.is_bookable ? '' : ' (unavailable)')"></option>
        </template>
      </select>
      @error('slot_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
  </div>
  <div class="mt-3">
    <label class="text-xs font-semibold text-slate-500 block mb-1.5">Total Amount (৳)</label>
    <input type="number" name="total_amount" x-model="totalAmount" step="0.01" min="0"
           class="w-48 text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 font-semibold">
    @error('total_amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>
</div>

{{-- 3: Payment --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
  <div class="flex items-center justify-between">
    <h3 class="text-sm font-semibold text-slate-700">3 — Initial Payment</h3>
    <label class="flex items-center gap-2 cursor-pointer select-none">
      <input type="checkbox" x-model="collectPayment" class="sr-only">
      <div class="w-9 h-5 rounded-full transition" :class="collectPayment?'bg-blue-600':'bg-slate-200'">
        <div class="mt-0.5 ml-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform" :class="collectPayment?'translate-x-4':''"></div>
      </div>
      <span class="text-xs text-slate-500">Collect now</span>
    </label>
  </div>
  <div x-show="collectPayment" x-cloak class="mt-4 grid grid-cols-3 gap-3">
    <div>
      <label class="text-xs font-semibold text-slate-500 block mb-1.5">Amount (৳)</label>
      <input type="number" name="initial_payment" x-model="payAmount" step="0.01" min="0"
             class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
      <label class="text-xs font-semibold text-slate-500 block mb-1.5">Method</label>
      <select name="payment_method" x-model="payMethod"
              class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
        <option value="cash">💵 Cash</option>
        <option value="bkash">📱 bKash</option>
        <option value="nagad">📱 Nagad</option>
        <option value="rocket">📱 Rocket</option>
        <option value="bank_transfer">🏦 Bank Transfer</option>
        <option value="cheque">📄 Cheque</option>
        <option value="credit">💳 Credit</option>
      </select>
    </div>
    <div x-show="['bkash','nagad','rocket','bank_transfer','cheque'].includes(payMethod)" x-cloak>
      <label class="text-xs font-semibold text-slate-500 block mb-1.5">Reference #</label>
      <input type="text" name="payment_reference"
             class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
    </div>
  </div>
</div>

{{-- 4: Recurring --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
  <div class="flex items-center justify-between">
    <h3 class="text-sm font-semibold text-slate-700">4 — Recurring</h3>
    <label class="flex items-center gap-2 cursor-pointer select-none">
      <input type="checkbox" name="is_recurring" value="1" x-model="isRecurring" class="sr-only">
      <div class="w-9 h-5 rounded-full transition" :class="isRecurring?'bg-blue-600':'bg-slate-200'">
        <div class="mt-0.5 ml-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform" :class="isRecurring?'translate-x-4':''"></div>
      </div>
      <span class="text-xs text-slate-500">Make recurring</span>
    </label>
  </div>
  <div x-show="isRecurring" x-cloak class="mt-4 space-y-3">
    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="text-xs font-semibold text-slate-500 block mb-1.5">Type</label>
        <select name="recurrence_type" x-model="recurType"
                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 block mb-1.5">Interval</label>
        <input type="number" name="recurrence_interval" value="1" min="1" max="12"
               class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
      </div>
    </div>
    <div x-show="recurType==='weekly'" x-cloak>
      <label class="text-xs font-semibold text-slate-500 block mb-2">Days</label>
      <div class="flex gap-1.5 flex-wrap">
        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $di => $dn)
        <label class="cursor-pointer">
          <input type="checkbox" name="recurrence_days_of_week[]" value="{{ $di }}" class="peer sr-only">
          <span class="w-10 h-10 flex items-center justify-center rounded-xl text-xs font-semibold border-2 border-slate-200 text-slate-500 peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white transition">{{ $dn }}</span>
        </label>
        @endforeach
      </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="text-xs font-semibold text-slate-500 block mb-1.5">End By</label>
        <select name="recurrence_end_type" x-model="endType"
                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
          <option value="on_date">Specific Date</option>
          <option value="after_count">After N occurrences</option>
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500 block mb-1.5" x-text="endType==='on_date'?'End Date':'Occurrences'"></label>
        <input x-show="endType==='on_date'" type="date" name="recurrence_end_date"
               class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
        <input x-show="endType==='after_count'" type="number" name="recurrence_end_after_count" value="4" min="2" max="52"
               class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500">
      </div>
    </div>
  </div>
</div>

{{-- 5: Participants --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-700">5 — Participants</h3>
    <button type="button" x-on:click="addP()"
            class="text-xs font-semibold text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition">+ Add</button>
  </div>
  <template x-for="(p,i) in participants" :key="i">
    <div class="grid grid-cols-12 gap-2 mb-2">
      <input :name="'participants['+i+'][name]'" x-model="p.name" placeholder="Name*"
             class="col-span-4 text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
      <input :name="'participants['+i+'][phone]'" x-model="p.phone" placeholder="Phone"
             class="col-span-4 text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
      <input :name="'participants['+i+'][note]'" x-model="p.note" placeholder="Note"
             class="col-span-3 text-sm border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
      <button type="button" x-on:click="removeP(i)"
              class="col-span-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition">✕</button>
    </div>
  </template>
  <p x-show="participants.length===0" class="text-xs text-slate-400 text-center py-2">No participants added.</p>
</div>

{{-- 6: Notes --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
  <h3 class="text-sm font-semibold text-slate-700 mb-3">6 — Notes</h3>
  <textarea name="notes" rows="3" placeholder="Internal notes…"
            class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('notes') }}</textarea>
</div>

</div>{{-- left col --}}

<div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sticky top-20">
    <h3 class="text-sm font-semibold text-slate-700 mb-4">Booking Summary</h3>
    <div class="space-y-2.5 text-sm divide-y divide-slate-50">
      <div class="flex justify-between pb-2">
        <span class="text-slate-500">Customer</span>
        <span class="font-medium text-slate-800 truncate ml-4" x-text="customerName||'—'"></span>
      </div>
      <div class="flex justify-between py-2">
        <span class="text-slate-500">Total</span>
        <span class="font-bold text-lg text-slate-800">৳<span x-text="parseFloat(totalAmount||0).toFixed(0)"></span></span>
      </div>
      <div x-show="collectPayment" class="flex justify-between py-2 text-green-700">
        <span>Paying Now</span>
        <span class="font-semibold">৳<span x-text="parseFloat(payAmount||0).toFixed(0)"></span></span>
      </div>
      <div x-show="collectPayment" class="flex justify-between pt-2 text-red-600">
        <span>Balance Due</span>
        <span class="font-semibold">৳<span x-text="Math.max(0,parseFloat(totalAmount||0)-parseFloat(payAmount||0)).toFixed(0)"></span></span>
      </div>
    </div>
    <button type="submit"
            class="mt-5 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-3 rounded-xl transition shadow-sm shadow-blue-900/20">
      Create Booking
    </button>
    <a href="{{ route('admin.bookings.index') }}"
       class="mt-2 block text-center text-xs text-slate-400 hover:text-slate-600">Cancel</a>
  </div>
</div>

</div>
</form>
@endsection
