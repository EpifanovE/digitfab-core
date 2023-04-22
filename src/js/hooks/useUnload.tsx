import {useRef, useEffect, useState} from 'react';

const useUnload = isDirty => {
    const cb = useRef<any>(null);
    const form = useRef<any>(null);

    const [submitClicked, setSubmitClicked] = useState(false);

    useEffect(() => console.log(submitClicked), [submitClicked])

    useEffect(() => {
        form.current = document.querySelector("form[name='post']");

        form.current.addEventListener("submit", e => {
            setSubmitClicked(true);
        });
    }, []);

    useEffect(() => {
        cb.current = e => {
            if (isDirty && !submitClicked) {
                e.preventDefault();
                e.returnValue = '';
            }
        }
    }, [isDirty, submitClicked]);

    useEffect(() => {
        const onUnload = (...args) => {
            cb.current?.(...args)
        };

        window.addEventListener("beforeunload", onUnload);

        return () => window.removeEventListener("beforeunload", onUnload);
    }, []);
};

export default useUnload;