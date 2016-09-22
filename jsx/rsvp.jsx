import React from "react";
import ReactDom from "react-dom";
import Modal from 'react-modal';
import Request from 'superagent';

let modalWindowStyle = {
    content : {
        top: '50%',
        left: '50%',
        right: 'auto',
        bottom: 'auto',
        transform: 'translate(-50%, -50%)',
        padding: 0,
        backgroundColor: '#f5f4d1',
        border: 'solid 1px #8BC34A'
    }
};

class RsvpModal extends React.Component {
    _handleClick(e) {
        var status = e.currentTarget.dataset.status;
        this.props.requestRsvp(status);
        this.props.closeModal();
    }

    onRequestClose() {
        this.props.closeModal();
    }

    render() {
        return (
            <Modal isOpen={this.props.display}
                   onRequestClose={this.onRequestClose.bind(this)}
                   style={modalWindowStyle}
            >
                <div>
                    <ul className="content rsvpSelector">
                        <li><p data-status="2" className="rsvpPart" onClick={this._handleClick.bind(this)}>御出席</p></li>
                        <li><p data-status="1" className="rsvpNonPart" onClick={this._handleClick.bind(this)}>御欠席</p></li>
                    </ul>
                </div>
            </Modal>
        );
    }
}

class Rsvp extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            rsvpModalDisplay: false
        };
    }

    openModal() {
        this.setState({
            rsvpModalDisplay: true
        });
    }

    closeModal() {
        this.setState({
            rsvpModalDisplay: false
        });
    }

    requestRsvp(status) {
        Request.post(window.location.href + '/rsvp')
            .send({token: this.props.token, status: status})
            .end((err, res) => {
                if(err) {
                    console.log(err);
                    window.alert('不明なエラーが発生しました。ページをリロードし再度試してください。')
                } else {
                    console.log(res);
                }
            });
    }

    render() {
        return (
            <section>
                <div className="rsvp">
                    <button className="rsvpButton" onClick={this.openModal.bind(this)}>出欠のご回答</button>
                </div>
                <RsvpModal display={this.state.rsvpModalDisplay} closeModal={this.closeModal.bind(this)} requestRsvp={this.requestRsvp.bind(this)} />
            </section>
        );
    }
}

ReactDom.render(
    <Rsvp token={document.getElementById('token').getAttribute('data-token')} />,
    document.getElementById('container')
);