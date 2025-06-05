"use client"

import * as React from "react"
import { format } from "date-fns"
import { CalendarIcon } from "lucide-react"

import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Calendar } from "@/components/ui/calendar"
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover"

interface DatePickerProps {
    placeholder?: string;
    value?: Date | undefined; // renamed from defaultDate
    onDateSelect?: (date: Date | undefined) => void;
}

export function DatePicker({
                               placeholder = "Pick a date",
                               value,
                               onDateSelect,
                           }: DatePickerProps) {
    const [date, setDate] = React.useState<Date | undefined>(value);

    // Sync internal state when external value changes
    React.useEffect(() => {
        setDate(value);
    }, [value]);

    const handleDateSelect = (selectedDate: Date | undefined) => {
        setDate(selectedDate);
        onDateSelect?.(selectedDate);
    };

    return (
        <Popover>
            <PopoverTrigger asChild>
                <Button
                    variant={"outline"}
                    className={cn(
                        "w-[11rem] justify-start text-left font-normal",
                        !date && "text-muted-foreground"
                    )}
                >
                    <CalendarIcon className="mr-2 h-4 w-4" />
                    {date ? format(date, "PPP") : <span>{placeholder}</span>}
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-auto p-0" align="start">
                <Calendar
                    mode="single"
                    selected={date}
                    onSelect={handleDateSelect}
                    initialFocus
                />
            </PopoverContent>
        </Popover>
    );
}
