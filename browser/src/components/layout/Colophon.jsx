import React from 'react';

import storageAvailable from '../../lib/storage-available.js';
import welcome from '../../../images/colophon_welcome.png';

export default function Colophon() {
    if (!storageAvailable('localStorage')) {
        return '';
    }
    // Prevent saving hidden status:
    // localStorage.clear();

    const [open, setOpen] = React.useState(true);

    function handleClose(event) {
        event.preventDefault();
        setOpen(false);
        localStorage.setItem('colophon', 'closed');
    }

    if (open && localStorage.getItem('colophon') !== 'closed') {
        return (
            <div className="container dark type-retro" style={{ position: 'fixed', zIndex: 999, right: 0, bottom: 0, left: 0, color: '#BBB' }}>
                <div style={{ padding: '50px 0 40px 0', margin: '0 auto', textAlign: 'center' }}>
                    Start Game is the secret
                    <br />
                    <a href="#close" title="hide this message and don't show it to me again" className="tooltip" onClick={handleClose}>Now Pay me for the door repair charge</a>
                </div>
                <div style={{ position: 'absolute', zIndex: 2, top:'20px', left: '50%', width:'192px', height:'16px', margin: '0 0 0 -96px', background: `url(${welcome}) no-repeat 0 0` }} />
                <div style={{ position: 'absolute', zIndex: 2, bottom:'0', left: '50%', width:'192px', height:'18px', margin: '0 0 0 -96px', background: `url(${welcome}) no-repeat 0 -16px` }} />
                <div style={{ position: 'absolute', zIndex: 1, right:'0', bottom: '0', left:'0', width:'100%', height: '18px', background: `url(${welcome}) repeat-x 0 -34px` }} />
            </div>
        );
    }

    return '';
}
