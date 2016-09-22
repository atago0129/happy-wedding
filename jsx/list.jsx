import React from "react";
import ReactDom from "react-dom";
import Modal from 'react-modal';
import Request from 'superagent';

class Item extends React.Component {
    _handleClick(e) {
        this.props.openModal("modal_" + this.props.itemKey);
    }

    render() {
        return (
            <li>
                <a href="javascript: void(0)" onClick={this._handleClick.bind(this)}>
                    <figure><img src={this.props.image} /></figure><p className="name">{this.props.name}</p>
                </a>
            </li>
        )
    }
}

class List extends React.Component {
    render() {
        var _this = this;
        return (
            <ul className="itemList">
                {this.props.itemList.map(function(item) {
                    return <Item key={item.key} name={item.name} image={item.image} itemKey={item.key} openModal={_this.props.openModal} />
                })}
            </ul>
        )
    }
}

let modalWindowStyle = {
    content : {
        top: '50%',
        left: '50%',
        right: 'auto',
        bottom: 'auto',
        transform: 'translate(-50%, -50%)',
        padding: 0
    }
};

class ItemModalWindow extends React.Component {
    clickSelect(e) {
        this.props.closeModal(this.props.modalKey);
        this.props.openConfirm("confirm_" + this.props.itemData.key);
    }

    clickCancel(e) {
        this.props.closeModal(this.props.modalKey);
    }

    onRequestClose() {
        this.props.closeModal(this.props.modalKey);
    }

    render() {
        return (
            <Modal isOpen={this.props.display}
                   onRequestClose={this.onRequestClose.bind(this)}
                   style={modalWindowStyle}
            >
                <div>
                    <h1 className="modalTitle">{this.props.itemData.name}</h1>
                    <p className="modalDescription">{this.props.itemData.description}</p>
                    <img className="modalImage" src={this.props.itemData.image} />
                    <ul className="modalSelectButtons">
                        <li><p onClick={this.clickSelect.bind(this)}>これにする</p></li>
                        <li><p onClick={this.clickCancel.bind(this)}>キャンセル</p></li>
                    </ul>
                </div>
            </Modal>
        );
    }
}

class ConfirmModalWindow extends React.Component {
    clickCancel(e) {
        this.props.closeModal(this.props.confirmKey);
    }

    clickDecision(e) {
        this.props.requestPresent(this.props.itemData.key);
        this.props.closeModal(this.props.confirmKey)
    }

    onRequestClose() {
        this.props.closeModal(this.props.confirmKey);
    }

    render() {
        return (
            <Modal isOpen={this.props.display}
                   onRequestClose={this.onRequestClose.bind(this)}
                   style={modalWindowStyle}
            >
                <div>
                    <h1 className="modalTitle">確認</h1>
                    <p className="modalDescription">以下の商品で確定します。よろしいですか？</p>
                    <h2 className="modalDescription">{this.props.itemData.name}</h2>
                    <ul className="modalSelectButtons">
                        <li><p onClick={this.clickCancel.bind(this)}>キャンセル</p></li>
                        <li><p onClick={this.clickDecision.bind(this)}>確定</p></li>
                    </ul>
                </div>
            </Modal>
        );
    }
}

class AlertModal extends React.Component {
    _handleClick(e) {
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
                    <h1 className="modalTitle">エラー</h1>
                    <p className="modalDescription">不明なエラーが発生しました。ページをリロードし、しばらくおいてから試してください。。</p>
                    <p className="modalDescription">何度もこのメッセージが表示される場合、お手数ですが新郎新婦までご連絡ください。</p>
                    <p className="modalDescription">エラーコード: {this.props.errorCode}</p>
                    <ul className="modalSelectButtons">
                        <li><p onClick={this._handleClick.bind(this)}>確認</p></li>
                    </ul>
                </div>
            </Modal>
        );
    }
}

class MessageModal extends React.Component {
    onRequestClose() {
        // nothing to do
    }

    render() {
        return (
            <Modal isOpen={this.props.display}
                   onRequestClose={this.onRequestClose.bind(this)}
                   style={modalWindowStyle}
            >
                <div>
                    <h1 className="modalTitle">お土産物登録完了</h1>
                    <p className="modalDescription">お土産物のご希望を承りました。当日お渡しできることを楽しみにしております。</p>
                    <p className="modalDescription">なお、諸般の事情によりご希望に添えない可能性がございますが、その際は何卒ご理解いただけますと幸いです。</p>
                    <ul className="modalSelectButtons">
                        <li><a href={location.href}><p>確認</p></a></li>
                    </ul>
                </div>
            </Modal>
        );
    }
}

class App extends React.Component {
    constructor(props) {
        super(props);
        this.initializeModal();
    }

    initializeModal() {
        var modalDisplay = {};
        var confirmDisplay = {};
        this.props.itemList.map(function(item) {
            modalDisplay["modal_" + item.key] = false;
            confirmDisplay["confirm_" + item.key] = false;
        });
        this.state = {
            modalDisplay: modalDisplay,
            showModalKey: null,
            confirmDisplay: confirmDisplay,
            confirmKey: null,
            alertModalDisplay: false,
            errorCode: 'unknown error',
            messageModalDisplay: false
        }
    }

    openItemModal(modalKey) {
        var modalDisplay = this.state.modalDisplay;
        modalDisplay[modalKey] = true;
        this.setState({
            modalDisplay: modalDisplay,
            showModalKey: modalKey
        });
    }

    closeItemModal(modalKey) {
        var modalDisplay = this.state.modalDisplay;
        modalDisplay[modalKey] = false;
        this.setState({
            modalDisplay: modalDisplay,
            showModalKey: null
        });
    }

    openConfirmModal(confirmKey) {
        var confirmDisplay = this.state.confirmDisplay;
        confirmDisplay[confirmKey] = true;
        this.setState({
            confirmDisplay: confirmDisplay,
            confirmKey: confirmKey
        });
    }

    closeConfirmModal(confirmKey) {
        var confirmDisplay = this.state.confirmDisplay;
        confirmDisplay[confirmKey] = false;
        this.setState({
            confirmDisplay: confirmDisplay,
            confirmKey: null
        });
    }

    closeOnBackGround(e) {
        var targetStyle = e.target.getAttribute('style');
        if (targetStyle !== null && targetStyle.indexOf('z-index: 10000;') !== -1) {
            var modalDisplay = this.state.modalDisplay;
            if (this.state.showModalKey) {
                modalDisplay[this.state.showModalKey] = false;
            }
            var confirmDisplay = this.state.confirmDisplay;
            if (this.state.confirmKey) {
                confirmDisplay[this.state.confirmKey] = false;
            }
            this.setState({
                modalDisplay: modalDisplay,
                showModalKey: null,
                confirmDisplay: confirmDisplay,
                confirmKey: null
            });
        }
    }

    openAlertModal() {
        this.setState({
            alertModalDisplay: true
        });
    }

    setAlertModalErrorCode(errorCode) {
        this.setState({
            errorCode: errorCode
        });
    }

    closeAlertModal() {
        this.setState({
            alertModalDisplay: false
        });
    }

    openMessageModal() {
        this.setState({
            messageModalDisplay: true
        });
    }

    requestPresent(giftId) {
        var _this = this;
        Request.post(window.location.href)
            .send({token: this.props.token, giftId: giftId})
            .end((err, res) => {
                var result = JSON.parse(res.text);
                if(err) {
                    console.log(res);
                    if (result && result.error && result.error.code) {
                        _this.setAlertModalErrorCode(result.error.code)
                    }
                    _this.openAlertModal();
                } else {
                    if (result.status !== 'ok') {
                        _this.setAlertModalErrorCode(result.error.code);
                        _this.openAlertModal();
                    } else {
                        _this.openMessageModal();
                    }
                }
            });
    }

    render() {
        var _this = this;
        return (
            <section id="mainBody" onClick={this.closeOnBackGround.bind(this)}>
                <List itemList={this.props.itemList} openModal={this.openItemModal.bind(this)} />
                {this.props.itemList.map(function(item) {
                    var key = "modal_" + item.key;
                    return <ItemModalWindow
                        key={key}
                        display={_this.state.modalDisplay[key]}
                        itemData={item}
                        modalKey={key}
                        closeModal={_this.closeItemModal.bind(_this)}
                        openConfirm={_this.openConfirmModal.bind(_this)}
                    />;
                })}
                {this.props.itemList.map(function(item) {
                    var key = "confirm_" + item.key;
                    return <ConfirmModalWindow
                        key={key}
                        confirmKey={key}
                        display={_this.state.confirmDisplay[key]}
                        closeModal={_this.closeConfirmModal.bind(_this)}
                        requestPresent={_this.requestPresent.bind(_this)}
                        itemData={item} />
                })}
                <AlertModal display={this.state.alertModalDisplay} closeModal={this.closeAlertModal.bind(this)} errorCode={this.state.errorCode} />
                <MessageModal display={this.state.messageModalDisplay} />
            </section>
        );
    }
}

var data = JSON.parse(document.getElementById('listDataProvider').getAttribute('data-list'));
ReactDom.render(
    <App token={document.getElementById('token').getAttribute('data-token')} itemList={data} />,
    document.getElementById('container')
);

