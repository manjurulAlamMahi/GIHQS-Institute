import LessonItem from "./LessonItem";


interface LessonData {
    id: number;
    title: string;
    description: string;
}

const lessonsList: LessonData[] = [
    {
        id: 1,
        title: "Lean Healthcare Foundations",
        description: "Introduction to Lean thinking, patient value, and the role of waste reduction in healthcare.",
    },
    {
        id: 2,
        title: "Value Stream Mapping in Healthcare",
        description: "See the patient journey more clearly and identify delays, duplication, and non-value-added steps.",
    },
    {
        id: 3,
        title: "5S in Healthcare",
        description: "Learn how organization, visibility, and workspace discipline support safety and workflow reliability.",
    },
    {
        id: 4,
        title: "Continuous Improvement & Kaizen",
        description: "Understand how small daily improvements build stronger systems and better care processes over time.",
    },
    {
        id: 5,
        title: "Lean Problem Solving in Healthcare",
        description: "Learn how Lean teams move beyond symptoms to identify root causes and improve process performance.",
    },
    {
        id: 6,
        title: "Standard Work in Healthcare",
        description: "Understand how standardization reduces harmful variation and supports safer, more reliable care.",
    },
    {
        id: 7,
        title: "Visual Management in Healthcare",
        description: "Use visible cues, labels, and status systems to improve communication, awareness, and response.",
    },
    {
        id: 8,
        title: "Patient Flow in Lean Healthcare",
        description: "Examine bottlenecks, delays, and throughput challenges across the patient care journey.",
    },
    {
        id: 9,
        title: "Lean Leadership in Healthcare",
        description: "Explore how leaders create daily improvement culture, remove barriers, and support frontline teams.",
    },
    {
        id: 10,
        title: "Sustaining Lean in Healthcare",
        description: "Learn how to maintain gains through standard work, visibility, leadership support, and daily habits.",
    }
];

export default function LearningPathTimeline() {
    return (
        <div className="pt-4 md:pt-8">
            {lessonsList.map((lesson, idx) => (
                <LessonItem
                    key={lesson.id}
                    step={lesson.id}
                    title={lesson.title}
                    description={lesson.description}
                    isLast={idx === lessonsList.length - 1}
                />
            ))}
        </div>
    );
}