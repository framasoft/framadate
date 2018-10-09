class Slot {
    constructor(text) {
        this.text = text || '';
    }
}

class Choice {
    constructor(day, slots) {
        this.day = day || '';
        this.slots = slots || [new Slot(), new Slot()];
    }
}

$(document).ready(function () {
    var app = new Vue({
        delimiters: ['%%', '%%'],
        el: '#date-poll',
        template: '#date-poll-component',
        data() {
            return {
                choices: JSON.parse(localStorage.getItem('current_poll')) || [new Choice(), new Choice()],
                interval: {
                    start: null,
                    end: null,
                },
                modal_open: false,
            };
        },
        created() {
            console.log('created choices', this.choices);
        },
        mounted() {
            console.log('mounted', this.choices);
        },
        computed: {
            twoDate: function() {
                return this.choices.filter(choice => !this.isChoiceCompletelyEmpty(choice)).length <= 2;
            },
            noSlots: function() {
                console.log(this.choiceSlotsEmpty(this.choices[0]));
                return this.choiceSlotsEmpty(this.choices[0]);
            }
        },
        watch: {
            choices: {
                handler(newList, oldList) {
                    console.log('choices changed');
                    let j = 0;
                    // while (j < this.choices.length) {
                    //     if (this.choices.length > 2) {
                    //         if (this.choices[j].day === '' && this.choices.length > (j + 1) && this.choices[j+1].day !== '') {
                    //             this.choices.splice(j, 1);
                    //             console.log('removing empty choice before value', this.choices);
                    //             break;
                    //         }
                    //         if (this.choices[j].day === '' && this.choices.length > (j + 1) && this.choices[j+1].day === '') {
                    //             this.choices.splice(j + 1, 1);
                    //             console.log('removing empty choice before another empty choice', this.choices);
                    //             break;
                    //         }
                    //     }
                    //     if (this.choices[j].text !== '' && this.choices.length === (j + 1)) {
                    //         console.log('adding extra empty choice');
                    //         this.choices.push(new Choice());
                    //         break;
                    //     }
                    //     j = j+1;
                    // }

                    if (this.choices.length >= 2 && this.choices[this.choices.length - 1].day !== '') {
                        const filteredList = this.choices.filter(choice => !this.isChoiceCompletelyEmpty(choice));
                        console.log('filtered list', filteredList);
                        if (filteredList.length >= 2) {
                            this.choices = filteredList;
                            this.choices.push(new Choice());
                            console.log('adding extra empty choice');
                        }
                    }
                    this.choices.forEach((choice) => {
                        let i = 0;
                        while (i < choice.slots.length) {
                            console.log('processing slot ', i);
                            if (choice.slots.length > 2) {
                                if (choice.slots[i].text === '' && choice.slots.length > (i + 1) && choice.slots[i+1].text !== '') {
                                    choice.slots.splice(i, 1);
                                    console.log('removing empty slot before value', choice.slots);
                                    break;
                                }
                                if (choice.slots[i].text === '' && choice.slots.length > (i + 1) && choice.slots[i+1].text === '') {
                                    choice.slots.splice(i + 1, 1);
                                    console.log('removing empty slot before another empty slot', choice.slots);
                                    break;
                                }
                            }
                            if (choice.slots[i].text !== '' && choice.slots.length === (i + 1)) {
                                console.log('adding extra empty slot');
                                choice.slots.push(new Slot());
                                break;
                            }
                            i = i+1;
                        }
                        // if (choice.slots.length >= 2 && choice.slots[choice.slots.length - 1].text !== '') {
                        //     console.log('choice slots before', choice.slots);
                        //     choice.slots = choice.slots.filter(slot => slot.text !== '');
                        //     console.log('choice slots after', choice.slots);
                        //     choice.slots.push(new Slot());
                        // }
                        console.log('slots nb', choice.slots.length);
                    });
                },
                deep: true,
            },
        },
        methods: {
            addChoice() {
                this.choices.push(new Choice());
            },
            removeChoice(index) {
                this.choices.splice(index, 1);
                if (this.choices.length < 2) {
                    this.choices.push(new Choice());
                }
            },
            removeLastChoice() {
                this.removeChoice(-1);
            },
            addSlot(choice) {
                choice.slots = [...choice.slots, ''];
            },
            removeLastSlot(choice) {
                choice.slots = choice.slots.slice(0, -1);
            },
            removeAllDays() {
                this.choices = [new Choice(), new Choice()];
            },
            removeAllSlots() {
                this.choices.forEach((choice) => {
                    choice.slots = ['', '', ''];
                });
            },
            addInterval(res) {
                if (res !== 'ok') {
                    return;
                }
                this.choices = this.choices.filter(choice => !this.isChoiceCompletelyEmpty(choice));
                while (dayjs(this.interval.start).isBefore(this.interval.end) || dayjs(this.interval.start).isSame(this.interval.end)) {
                    this.choices.push(new Choice(this.interval.start));
                    this.interval.start = dayjs(this.interval.start).add(1, 'day').format('YYYY-MM-DD');
                }
                this.choices.push(new Choice());
            },
            onSubmit(e) {
                localStorage.setItem('current_poll', JSON.stringify(this.choices));
            },
            copyTimesFromFirstDay() {
                i = 1;
                slots = this.choices[0].slots;
                while (i < this.choices.length) {
                    let newChoice = new Choice(this.choices[i].day, slots.slice());
                    this.choices.splice(i, 1, newChoice);
                    i = i+1;
                }
            },
            isChoiceCompletelyEmpty(choice) {
                return this.choiceDayEmpty(choice) && this.choiceSlotsEmpty(choice);
            },
            choiceDayEmpty(choice) {
                return choice.day === '';
            },
            choiceSlotsEmpty(choice) {
                return choice.slots.every((slot) => slot.text === '');
            }
        },
    });
});
