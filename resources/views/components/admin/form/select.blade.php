<div x-data="{
    open: false,
    options: ['India', 'USA', 'UK', 'Japan'],
    selected: [],
    toggle(option) {
        if (this.selected.includes(option)) {
            this.selected = this.selected.filter(i => i !== option)
        } else {
            this.selected.push(option)
        }
    }
}">
    <div class="relative">
        <button type="button" @click="open = !open" class="w-full border rounded-lg px-3 py-2 text-left">
            <span x-show="selected.length === 0">Select options</span>
            <span x-show="selected.length > 0" x-text="selected.join(', ')"></span>
        </button>

        <div x-show="open" @click.outside="open = false"
            class="absolute mt-2 w-full bg-white border rounded-lg shadow-lg max-h-48 overflow-y-auto">
            <template x-for="option in options" :key="option">
                <label class="flex items-center px-3 py-2 cursor-pointer hover:bg-gray-100">
                    <input type="checkbox" :value="option" @change="toggle(option)"
                        :checked="selected.includes(option)" class="mr-2 text-blue-600 rounded focus:ring-blue-500">
                    <span x-text="option"></span>
                </label>
            </template>
        </div>
    </div>
</div>
